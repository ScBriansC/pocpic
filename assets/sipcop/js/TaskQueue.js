// *****************************************************************************
// TaskQueue v1.01
// Original Author: RK
// *****************************************************************************
//
// SUMMARY:
//
//  Task scheduling and "multitasking" classes for Javascript.
//
// FEATURES:
//
//  - tasks can be added / removed at any time
//  - tasks can be prioritized
//  - support for asynchronous tasks (e.g. XmlHttpRequest's)
//  - multiple task queues can be used for preempting asynchronous tasks, and
//    to group tasks that should block each other
//    
// EXAMPLES:
//
//    // Simplest usage
//    var taskQueue = new TaskQueue();
//    taskQueue.schedule("alert('there')", 1);
//    taskQueue.schedule("alert('hello')", 0);
//
//    // Custom tasks
//    MessageTask = function(msg) {
//      this.taskId = null; // set when scheduled
//      this.msg = msg;
//      this.priority = 1;
//      this.description = "Display message '" + msg + "'";
//      MessageTask.prototype.run = function() {
//        alert(msg);
//      }
//    }
//    taskQueue.schedule(new MessageTask("Nice to meet you"));
//
// USAGE NOTES:
//
//  Any class that provides a run() method can be queued as a task.  A
//  GenericTask class is provided to wrap javascript functions, closures, etc
//  on the fly, but you can also create and schedule your own classes.
//
//  The TaskQueue periodically examines its scheduled tasks and runs the one
//  with the smallest priority number.  A timer is used to keep the browser
//  responsive between tasks, and so that tasks are invoked from a clean stack.
//  The timer is only enabled when the queue is not empty.
//  
//  Only one task may run at a time.  A single TaskQueue is not "preemptive",
//  and will not launch a new task until the current one is complete.
//
//  If your task must perform a lot of work, it is suggested you split the
//  work into "chunks" to avoid locking up the browser.  Tasks may requeue
//  themselves at the end of their run() method to resume work at a later time.
//
//  The TaskQueue can be paused and resumed at will.  If a task is active when
//  a pause is requested, the pause will be "acknowledged" as soon as the
//  current task is complete.
//
//  Your task is automatically assigned a taskId when you schedule it.  Your
//  task should also provide the "priority" and "description" fields.  If
//  not provided, they are created automatically when your task is scheduled.
//  
// ASYNCHRONOUS TASKS:
//
//  If your run() method begins an asynchronous operation (such as sending an
//  XmlHttpRequest) and must return before work is complete, you may request
//  a hold to keep the task marked active until you later call the release()
//  method.  This is similar to pausing the queue, but is designed for use
//  by tasks themselves.  It can be used to synchronize asynchronous
//  operations (i.e. when you don't want any other tasks to run until some
//  external event is fired).  The ImagePreloadTask makes use of the feature
//  to avoid loading new images until the current image is complete.
//
//  When using this feature, be sure you eventually do call release(), or you
//  will hold up the queue indefinitely.  A timeout can be specified after
//  which the hold will be automatically released and your task "abandoned".
//  If an expired() method is implemented by your task it is invoked if this
//  occurs.
//
//  If you want the ability to preempt asynchronous tasks, you can create
//  multiple TaskQueue's.  For example, you can create one TaskQueue to
//  synchronize all tasks that require a server connection, and another
//  TaskQueue for UI tasks that operate entirely on the client side.
//  When connection tasks hold up the first queue, UI tasks can still run on
//  the second queue.
//
// REVISIONS:
// 
//  v1.01, 2012-Nov-06, RK
//  - remove references to log() and make code public
//  v1.00, 2009-Jun-29, RK
//  - initial release
//
//
// *****************************************************************************

// Generic wrapper to turn any function or string of javascript into a task
function GenericTask(run) {  
  if (run instanceof Function) {
    this.run = run;
    //this.description = run.toString().substring(0, 50);
  } else if (typeof(run) == 'string') {
    this.run = function(){eval(run)};
    //this.description = run.substring(0, 50);
  } else {
    throw 'Must specify callback function';    
  }
  // Note: taskId, priority and description properties are set when task is
  // scheduled.
}

function Tasks() {

  this.items = new Array();
  this._dirty = false;

  Tasks.prototype.add = function(task) {
    this.items.push(task);
    this._dirty = true;
    return task;
  }

  Tasks.prototype.idToIndex = function(id) {
    for (var i = 0; i < this.items.length; i++) {
      if (this.items[i].taskId == id) return i;
    }
    return null;
  }

  // Requires that priority has been set on all tasks
  Tasks.prototype.sort = function() {
    if (!this._dirty) return;
    this.items.sort(Tasks.prototype.priorityComparer);
    this._dirty = false;
  }

  Tasks.prototype.priorityComparer = function (taskA, taskB) {
    return taskA.priority - taskB.priority;
  }

  Tasks.prototype.find = function(id) {
    if (!id) return null;
    return this.items[this.idToIndex(id)];
  }

  Tasks.prototype.remove = function(id) {    
    var idx = this.idToIndex(id);
    if (idx == null) throw 'Task with id ' + id + ' not found.';
    if (idx == 0) {
      this.items.shift();
    } else {
      this.items.splice(idx, 1);
    }
    this._dirty = true;
  }

  // Requires that priority has been set on all tasks
  Tasks.prototype.smallestPriority = function () {
    if (this.count == 0) return null;
    this.sort();
    return this.items[0];
  }

  Tasks.prototype.count = function () {
    return this.items.length;
  }

}

function TaskQueue(timeBetweenTasks) {  
  
  this.tickInterval = timeBetweenTasks ? timeBetweenTasks : 10;
  this.tasks = new Tasks();
  this.pauseRequested = false;
  this.pauseCallback = null;
  this.paused = false;
  this.activeTask = null;
  this.timerId = null;
  
  this.held = false;
  this.heldTimerId = null;
  
  TaskQueue.prototype.generateTaskId = function() {
    if (!TaskQueue.prototype._nextId) TaskQueue.prototype._nextId = 1;
    return TaskQueue.prototype._nextId++;
  }
  
  // Pause the task spooler.  If a task is currently executing, the pause
  // will be delayed until its execution is complete.  A callback function
  // can be provided which will be invoked when the pause is acknowledged.
  // Note if no tasks are active, the callback will be invoked immediately.
  TaskQueue.prototype.pause = function(callback) {
    if (this.paused) return;
    this.pauseRequested = true;
    this.pauseCallback = callback;
    this._tryAcknowledgePause();
  }
  
  TaskQueue.prototype.resume = function() {
    if(this.paused) {
      this.paused = false;
      this._startTimer();
    } else {
      this.pauseRequested = false;
      this.pauseCallback = null;
    }
  }
  
  // Prepares a task to be scheduled.
  TaskQueue.prototype._prepareTask = function(task, priority, description) {
    if (task == null) throw 'Must specify task';
    if (typeof task == "string" || task instanceof Function) {
      task = new GenericTask(task); // wrap
    }
    // Parameters specified override any already set on task
    if (priority) task.priority = priority;
    if (description) task.description = description;
    // Set defaults for any properties that don't exist
    if (!task.priority) task.priority = 0;
    if (!task.description) task.description = '';
    // Generate a unique id (changes each time task is scheduled)
    task.taskId = TaskQueue.prototype.generateTaskId();    
    return task;
  }
  
  // Places a task in the queue
  TaskQueue.prototype.schedule = function(task, priority, description) {
    task = this._prepareTask(task, priority, description);
    this.tasks.add(task);
    if (!this.paused) this._startTimer();
    return task;
  }

  TaskQueue.prototype._tryAcknowledgePause = function() {
    if (!this.pauseRequested || this.activeTask) return false;    
    if (this.timerId) clearTimeout(this.timerId);
    this.timerId = null;
    this.paused = true;
    this.pauseRequested = false;
    if (this.pauseCallback) {
      try {this.pauseCallback()}
      catch(ex){
        //log(ex)
      }
      this.pauseCallback = null;
    }
    return true;
  }
  
  TaskQueue.prototype._tick = function() {
    this.timerId = null;
    if (this.activeTask || this.paused || this.held) {
      throw 'TaskQueue Detected invalid state in _tick';
    }
    if (this._tryAcknowledgePause()) return;
    if (this.tasks.count() == 0) return; // no more tasks
    this._doTask();
    if (this.held) return;
    if (this._tryAcknowledgePause()) return;
    this._startTimer();
  }
  
  TaskQueue.prototype._startTimer = function() {
    if (this.paused || this.activeTask || this.timerId) return;
    if (this.tasks.count() == 0) return;
    var _self = this;
    this.timerId = setTimeout(function(){_self._tick()}, this.tickInterval);    
  }
  
  TaskQueue.prototype._doTask = function() {
    this.activeTask = this.tasks.smallestPriority();
    this.tasks.remove(this.activeTask.taskId);
    if (this.activeTask.run instanceof Function) {
      //log('Running task #' + this.taskToString(this.activeTask));
      try {this.activeTask.run()}
      catch(ex) {
        //log(ex)
      }
    } else {
      //log('No run() method on task #' + this.taskToString(this.activeTask));
    }
    if (!this.held) this.activeTask = null;
  }
  
  // Causes the Task Manager to keep the current task active until release()
  // is called or (optionally) a timeout occurs.  Called by a task when it is
  // about to perform asynchronous work and it wants to block other tasks from
  // running.
  TaskQueue.prototype.hold = function(timeout) {
    if (this.held) throw 'Already in hold mode';
    if (!this.activeTask) throw 'No task currently running';
    this.held = true;
    if (timeout) {
      var _self = this;
      this.heldTimerId = setTimeout(function(){_self._holdExpired()}, timeout);
    }
    return this.activeTask.taskId;
  }
  
  // Release a previously aquired hold.  Called by a task when it has
  // completed its work and the Task Manager can resume processing tasks.
  // Must specify taskId to detect if being called from an abandoned task
  // who's hold has expired.
  TaskQueue.prototype.release = function(taskId) {
    if (!taskId) taskId = this.activeTask.taskId; // for debugging
    if (!taskId) throw 'Must specify taskId to release';
    if (!this.activeTask || !this.held) return;
    if (taskId != this.activeTask.taskId) return;
    this._resumeAfterHold();
  }  
  
  TaskQueue.prototype._holdExpired = function() {
    this.heldTimerId = null;
    if (!this.held || !this.activeTask)
      throw 'TaskQueue detected invalid state in _holdExpired'
    //log('Hold expired for task #' + this.taskToString(this.activeTask));
    if (this.activeTask.expired instanceof Function) {
      try {this.activeTask.expired()}
      catch(ex){
        //log(ex)
      }
    }
   this._resumeAfterHold();
  }
  
  TaskQueue.prototype._resumeAfterHold = function() {
    if (this.heldTimerId) clearTimeout(this.heldTimerId);
    this.heldTimerId = null;
    this._holdId = null;
    this.held = false;
    // Paste commands normally performed after task.run() returns
    this.activeTask = null;
    if (this._tryAcknowledgePause()) return;
    this._startTimer();    
  }
  
  TaskQueue.prototype.taskToString = function(task) {
    var s = '' + task.taskId;
    if (task.description && task.description != '') {
      s += ': ' + task.description;
    }
    return s;
  }
  
  // List tasks to console (for debugging)
  TaskQueue.prototype.listTasks = function() {
    for (var i = 0; i < this.tasks.count(); i++) {
      //log('Task #' + this.taskToString(this.tasks.items[i]));
    }
  }

  // for debugging only
  TaskQueue.prototype.clearAll = function() {
    this.tasks = new Tasks();
    this.pauseRequested = false;
    this.pauseCallback = null;
    this.paused = false;
    if (this.timerId) clearTimeout(this.timerId);
    this.timerId = null;  
    this.held = false;
    if (this.heldTimerId) clearTimeout(heldTimerId);
    this.heldTimerId = null;
  }
  
}