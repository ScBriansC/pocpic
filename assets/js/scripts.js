Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

Number.prototype.toFixedDown = function(digits) {
    var re = new RegExp("(\\d+\\.\\d{" + digits + "})(\\d)"),
        m = this.toString().match(re);
    return m ? parseFloat(m[1]) : this.valueOf();
};

function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };

(function ($) {
    "use strict";
    $(document).ready(function () {
        $(document).on("keydown", disableF5);
        $.fn.extend({
             trackChanges: function() {
               $(":input",this).change(function() {
                  $(this.form).data("changed", true);
               });
             }
             ,
             isChanged: function() { 
               return this.data("changed"); 
             }
        });

        
        /*==Left Navigation Accordion ==*/
        if ($.fn.dcAccordion) {
            $('#nav-accordion').dcAccordion({
                eventType: 'click',
                autoClose: true,
                saveState: true,
                disableLink: true,
                speed: 'slow',
                showCount: false,
                autoExpand: true,
                classExpand: 'dcjq-current-parent'
            });
        }
        
        /*==Nice Scroll ==*/
        if ($.fn.niceScroll) {


            $(".leftside-navigation").niceScroll({
                cursorcolor: "#1FB5AD",
                cursorborder: "0px solid #fff",
                cursorborderradius: "0px",
                cursorwidth: "3px"
            });

            $(".leftside-navigation").getNiceScroll().resize();
            if ($('#sidebar').hasClass('hide-left-bar')) {
                $(".leftside-navigation").getNiceScroll().hide();
            }
            $(".leftside-navigation").getNiceScroll().show();

            

        }

        /*==Sidebar Toggle==*/

        $(".leftside-navigation .sub-menu > a").click(function () {
            var o = ($(this).offset());
            var diff = 80 - o.top;
            if (diff > 0)
                $(".leftside-navigation").scrollTo("-=" + Math.abs(diff), 500);
            else
                $(".leftside-navigation").scrollTo("+=" + Math.abs(diff), 500);
        });



        $('.sidebar-toggle-box .fa-bars').click(function (e) {

            $(".leftside-navigation").niceScroll({
                cursorcolor: "#1FB5AD",
                cursorborder: "0px solid #fff",
                cursorborderradius: "0px",
                cursorwidth: "3px"
            });

            $('#sidebar').toggleClass('hide-left-bar');
            if ($('#sidebar').hasClass('hide-left-bar')) {
                if($(window).width()<751){
                    SipcopJS.crearCookie('sidebar', 'abierto', 1);
                }else{
                    SipcopJS.crearCookie('sidebar', 'cerrado', 1);
                }
                
                $(".leftside-navigation").getNiceScroll().hide();
            }else{
                SipcopJS.crearCookie('sidebar', 'abierto', 1);
                
            }
            $(".leftside-navigation").getNiceScroll().show();
            $('#main-content').toggleClass('merge-left');
            e.stopPropagation();
            

            if ($('.header').hasClass('merge-header')) {
                $('.header').removeClass('merge-header')
            }

            $(window).resize();
        });

        $('.header,#main-content,#sidebar').click(function () {
           

            if ($('.header').hasClass('merge-header')) {
                $('.header').removeClass('merge-header')
            }


        });


        $('.panel .tools .fa').click(function () {
            var el = $(this).parents(".panel").children(".panel-body");
            if ($(this).hasClass("fa-chevron-down")) {
                $(this).removeClass("fa-chevron-down").addClass("fa-chevron-up");
                el.slideUp(200);
            } else {
                $(this).removeClass("fa-chevron-up").addClass("fa-chevron-down");
                el.slideDown(200); }
        });



        $('.panel .tools .fa-times').click(function () {
            $(this).parents(".panel").parent().remove();
        });

        // tool tips

        $('.tooltips').tooltip();

        // popovers

        $('.popovers').popover();

        $(".decimal2").numeric({ decimal : ".",  negative : false, scale: 2 });
        $(".digitos").numeric({ decimal : ".",  negative : false, scale: 0 });


    });
    
    $(window).resize(function(){
        recalcularCharts();
    });

    $(".decimal1").numeric({ decimalPlaces: 1 });
    $(".decimal2").numeric({ decimalPlaces: 2 });
    $(".digits").numeric({ decimalPlaces: 0 });


})(jQuery);

function recalcularCharts(){
    setTimeout(function(){
        if(typeof Highcharts != 'undefined'){
            $.each(Highcharts.charts, function(iChar, oChart){
                if(typeof oChart != 'undefined'){
                    oChart.redraw();
                    oChart.reflow();
                }
            });
        }
    }, 500);
}

(function(a){a.createModal=function(b){defaults={title:"",message:"",closeButton:true,scrollable:false};var b=a.extend({},defaults,b);var c=(b.scrollable===true)?'style="max-height: 420px;overflow-y: auto;"':"";html='<div class="modal fade" id="myModal">';html+='<div class="modal-dialog">';html+='<div class="modal-content">';html+='<div class="modal-header">';html+='<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';if(b.title.length>0){html+='<h4 class="modal-title">'+b.title+"</h4>"}html+="</div>";html+='<div class="modal-body" '+c+">";html+=b.message;html+="</div>";html+='<div class="modal-footer">';if(b.closeButton===true){html+='<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>'}html+="</div>";html+="</div>";html+="</div>";html+="</div>";a("body").prepend(html);a("#myModal").modal().on("hidden.bs.modal",function(){a(this).remove()})}})(jQuery);

//Activar tooltip en DataTable
function tooltipDT(nTd, sData, oData, iRow, iCol) {
    $("a", nTd).tooltip();
}