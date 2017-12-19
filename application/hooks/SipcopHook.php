<?php 
class SipcopHook {
	private $CI;

    function __construct()
    {
        $this->CI =& get_instance();
        if(!isset($this->CI->session)){ 
              //$this->CI->load->library('database'); 
        }
    }

   function finalizar()
   {

        if(@$this->CI->db){
        	@$this->CI->db->close();
        }
    }
}