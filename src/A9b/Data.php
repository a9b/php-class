<?php
namespace A9b;

/**
 * データユーティリティ
 *
 * @version    1.00
 * @since      2013/12/03 17:55:13
 * @author     a9b
 * @copyright  (C)2013 a9b All Rights Reserved
 * @package    none
 *
 * @usage
 * include_once("./Data.php");
 * $file = new Data("./datasource/");
 *  
 * $options = array(
 *   "encode"=>"UTF-8",
 *   "return_type"=>"array",//string
 * );
 * $d = $file->setCSV("hoge",$options)->get();
 * $d = $file->set("hoge.csv")->to_array()->to_array(false,",")->encode("UTF-8")->get();
 */

class Data {

  /** 
   * none
   * @param string 
   * @access private 
   */
  var $dir;

  /** 
   * none
   * @param string 
   * @access private 
   */
  var $path;

  /** 
   * none
   * @param array() 
   * @access private 
   */
  var $data;

  const ERROR_NOT_FOUND_PATH = "ファイルが存在しません";

  /**
   * none
   * @since 2013/12/03 17:57:53
   * @author     a9b
   * @param      @string $dir  string 
   * @return     @object
   */ 
  public function __construct($dir)
  {
    $this->dir = $dir;
    if (!file_exists($this->dir))
    {
      $this->_error(self::ERROR_NOT_FOUND_PATH,$this->dir);
    }//if

    if (substr($this->dir,-1) !== "/")
    {
      $this->dir . "/";
    }//if

    return $this;
  }//function


  /**
   * none
   * @since 2013/12/03 17:55:59
   * @author     a9b
   * @param      @string $file  filename
   * @param      @mixed $option  option fileget 
   * @return     @mixed boolean
   */ 
  public function setFile($file,$options=null)
  {
    $this->_setFile($file);
    $this->data = file_get_contents($this->path);
    $this->data = str_replace("\r\n","\n",$this->data);

    # encoding
    if ($options["encode"])
    {
      $this->encode($this->data,$options["encode"]);
    }//if

    # return_type
    if ("array" === $options["return_type"])
    {
      $this->to_array($this->data);
    }//if

    return $this;
  }//function


  /**
   * none
   * @since 2013/12/03 17:55:59
   * @author     a9b
   * @param      @string $file  filename
   * @param      @mixed $options  option fileget 
   * @param      @string $delimiter  divides 
   * @return     @object
   */ 
  public function setCsv($file, $options=null, $delimiter = ",")
  {
    $options["return_type"] = "array";
    $file = str_replace(".csv","",$file) . ".csv";
    $this->setFile($file, $options);
    $this->data = $this->_to_array($this->data,$delimiter);

    return $this;
  }//function


  /**
   * none
   * @since 2013/12/03 17:55:58
   * @author     a9b
   * @param      @string $file  filename
   * @param      @mixed $options  option fileget 
   * @param      @string $delimiter  divides 
   * @return     @object
   */ 
  public function setTsv($file, $options=null, $delimiter = "\t")
  {
    $options["return_type"] = "array";
    $file = str_replace(".tsv","",$file) . ".tsv";
    $this->setFile($file, $options);
    $this->data = $this->_to_array($this->data,$delimiter);

    return $this;
  }//function


  /**
   * none
   * @since 2013/12/03 17:55:31
   * @author     a9b
   * @param      @string $str  string 
   * @return     @mixed  array() or false(boolean)
   */ 
  public function get()
  {
    if (is_null($this->data))
    {
      return false;
    }//if

    return $this->data;
  }//function


  /**
   * It divides.
   * @since 2013/12/03 18:59:19
   * @author     a9b
   * @param      @mixed $mix  array or string 
   * @return     @mixed  array()
   */ 
  public function to_array($mix = false, $delimiter="\n")
  {
    if (false === $mix)
    {
      $mix = $this->data;
    }//if 

    $this->data = $this->_to_array($mix,$delimiter);

    return $this;
  }//function


  /**
   * It divides. In arrangement, there is it recursively.
   * @since 2013/12/03 18:59:19
   * @author     a9b
   * @param      @mixed $mix  array or string 
   * @return     @mixed  array()
   */ 
  private function _to_array($mix = false, $delimiter="\n")
  {
    if (is_array($mix))
    {
      foreach ($mix as $k=>$v)
      {
        $r[] = explode($delimiter, $v);
      }
    }
    else
    {
      $r = explode($delimiter, $mix);
    }//if

    return $r;
  }//function


  /**
   * none
   * @since 2013/12/03 19:01:33
   * @author     a9b
   * @param      @string $mix string or array() 
   * @param      @string $to string 
   * @param      @string $from string 
   * @return     @mixed array() or string
   */ 
	public function encode($mix = false, $to="UTF-8", $from=null){
    if (false === $mix)
    {
      $mix = $this->data;
    }//if 

    $this->data = $this->_encode($this->data, $to, $from);

		return $this;
	}//function


 /**
   * none
   * @since 2013/12/03 19:45:49
   * @author     a9b
   * @param      @string $mix string or array() 
   * @param      @string $to string 
   * @param      @string $from string 
   * @return     @mixed array() or string
  */ 
  private function _encode($mix = false, $to="UTF-8", $from=null)
  {
		mb_language("Japanese");
		$from_org = $from;
		
		if(is_array($mix)){
			foreach($mix as $key => $val){
			$from = $from_org;
				if(is_array($val)){
					$mix[$key] = $this->_encode($val,$to);
				}else{
					if(is_null($from)){$from=mb_detect_encoding($val);}
					if($from != $to){$mix[$key]=mb_convert_encoding($val,$to,$from);}
				}
			}
		}
		else{
      if(is_null($from) and $from = mb_detect_encoding($mix))
      {
        $mix = mb_convert_encoding($mix,$to,$from);
      }
    }

    return $mix;
  }//function


 /**
   * none
   * @since 2013/12/03 19:51:24
   * @author     a9b
   * @param      @string $file string 
   * @return     true
  */ 
  private function _setFile($file)
  {
    $this->path = $this->dir . $file;
    if (!file_exists($this->path))
    {
      $this->_error(self::ERROR_NOT_FOUND_PATH,$this->path);
    }//if

    return true;
  }//function


  /**
   * none
   * @since 2013/12/03 18:01:51
   * @author     a9b
   * @param      @string $str  string 
   * @return     @mixed  array() or false(boolean)
   */ 
  private function _error($code,$str=null)
  {
    echo __FILE__;
    echo ":";
    echo __LINE__;
    echo "\t";
    echo $code;
    echo $str;
    exit;
  }//function

}
