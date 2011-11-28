<?php

interface I_FormPrimitive
{
  public function out();
}

////////////////////////////////////////////////////////////////////////////////

class Form implements I_FormPrimitive
{
  function __construct($use_file_field=null)
  {
    $this->fieldlist=array();
    if($use_file_field)
      $this->file=true;
    else
      $this->file=false;
  }

  public function add($o)
  {
    array_push($this->fieldlist,$o);
    return $this;
  }

  public function out()
  {
    if($this->file)
      $buf="<form enctype=\"multipart/form-data\" method=\"post\" action=\"index.php\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"8388608\" /><ul>";
    else
      $buf="<form method=\"post\" action=\"index.php\"><ul>";

    foreach($this->fieldlist as $f)
      {
	$buf.=$f->out();
      }
    $buf.="<li><input type=\"submit\" name=\"submit\" value=\"Submit\" /><input type=\"reset\" value=\"Reset\" /></li></ul></form>\n";
    return $buf;
  }

  protected $fieldlist;
}

////////////////////////////////////////////////////////////////////////////////

class FormOptgroup implements I_FormPrimitive
{
  function __construct($label)
  {
    $this->label=$label;
    $this->options=array();
    $this->default=null;
  }

  public function opt($id,$elem)
  {
    array_push($this->options,array($id,$elem));
    return $this;
  }

  public function value($id)
  {
    $this->default=$id;
    return $this;
  }

  public function out()
  {
    $buf="<optgroup label=\"".$this->label."\">";
    foreach($this->options as $o)
      {
	if($this->default==$o[0])
	  {
	    $sel=" selected=\"selected\"";
	  }
	else
	  {
	    $sel='';
	  }

	$buf.="<option value=\"".$o[0]."\"".$sel.">".$o[1]."</option>";
      }
    $buf.="</optgroup>";
    return $buf;
  }

  private $options;
  private $default;
  private $label;
}

////////////////////////////////////////////////////////////////////////////////

class FormMessage implements I_FormPrimitive
{
  function __construct($msg)
  {
    $this->msg=$msg;
    $this->class=null;
  }

  public function css($class)
  {
    $this->class=$class;
    return $this;
  }

  public function out()
  {
    return "<li class=\"".$this->class."\">".$this->msg."</li>";
  }

  private $msg;
  private $class;
}

////////////////////////////////////////////////////////////////////////////////

class FormPlaceholder implements I_FormPrimitive
{
  function __construct($id)
  {
    $this->id=$id;
    $this->class=null;
  }

  public function css($class)
  {
    $this->class=$class;
    return $this;
  }

  public function out()
  {
    return "<li id=\"".$this->id."\" class=\"".$this->class."\"></li>";
  }

  private $id;
  private $class;
}

////////////////////////////////////////////////////////////////////////////////

class FormFieldset extends Form
{
  function __construct()
  {
    parent::__construct();
    $this->class=null;
    $this->legend=null;
  }

  public function css($class)
  {
    $this->class=$class;
    return $this;
  }

  public function legend($t)
  {
    $this->legend=$t;
    return $this;
  }

  public function out()
  {
    $buf="<li class=\"".$this->class."\"><fieldset><legend>".$this->legend."</legend><ul>";
    foreach($this->fieldlist as $f)
      {
	$buf.=$f->out();
      }
    $buf.="</ul></fieldset></li>";
    return $buf;
  }

  private $class;
  private $legend;
}

////////////////////////////////////////////////////////////////////////////////

abstract class A_FormField implements I_FormPrimitive
{
  function __construct($n)
  {
    $this->name=$n;
    $this->label=null;
    $this->class=null;
  }

  public function label($t)
  {
    $this->label=$t;
    return $this;
  }

  public function css($t)
  {
    $this->class=$t;
    return $this;
  }

  protected $name;
  protected $label;
  protected $class;
}

////////////////////////////////////////////////////////////////////////////////

class FormText extends A_FormField
{
  function __construct($n)
  {
    parent::__construct($n);
    $this->size=20;
    $this->maxlength=255;
    $this->default=null;
    $this->reverse=false;
  }

  public function limit($i)
  {
    $this->maxlength=$i;
    if($i<$this->size)
      {
	$this->size=$i;
      }
    return $this;
  }

  public function reverse()
  {
    $this->reverse=true;
    return $this;
  }

  public function value($def)
  {
    $this->default=$def;
    return $this;
  }

  public function out()
  {
    $label="<label for=\"".$this->name."\">".$this->label."</label>";
    $field="<input type=\"text\" name=\"".$this->name."\" id=\"".$this->name."\" size=\"".$this->size
      ."\" maxlength=\"".$this->maxlength."\" value=\"".htmlentities($this->default)."\" />";

    if($this->reverse)
      return "<li class=\"".$this->class."\">".$field.$label."</li>";
    else
      return "<li class=\"".$this->class."\">".$label.$field."</li>";
  }

  private $default;
  private $size;
  private $maxlength;
  private $reverse;
}

////////////////////////////////////////////////////////////////////////////////

class FormPassword extends FormText
{
  function __construct($n)
  {
    parent::__construct($n);
  }

  public function out()
  {
    $label="<label for=\"".$this->name."\">".$this->label."</label>";
    $field="<input type=\"password\" name=\"".$this->name."\" id=\"".$this->name."\" size=\"".$this->size
      ."\" maxlength=\"".$this->maxlength."\" value=\"".htmlentities($this->default)."\" />";

    if($this->reverse)
      return "<li class=\"".$this->class."\">".$field.$label."</li>";
    else
      return "<li class=\"".$this->class."\">".$label.$field."</li>";
  }
}

////////////////////////////////////////////////////////////////////////////////

class FormTextarea extends A_FormField
{
  function __construct($n)
  {
    parent::__construct($n);
    $this->rows=4;
    $this->cols=40;
    $this->default=null;
  }

  public function size($rows,$cols)
  {
    $this->rows=$rows;
    $this->cols=$cols;
    return $this;
  }

  public function value($def)
  {
    $this->default=$def;
    return $this;
  }

  public function out()
  {
    return "<li class=\"".$this->class."\"><label for=\"".$this->name."\">".$this->label."</label><textarea "
      ."name=\"".$this->name."\" id=\"".$this->name."\" rows=\"".$this->rows."\" cols=\"".$this->cols."\">"
      .htmlentities($this->default)."</textarea></li>";
  }

  private $rows;
  private $cols;
  private $default;
}

////////////////////////////////////////////////////////////////////////////////

class FormRadio extends A_FormField
{
  function __construct($n)
  {
    parent::__construct($n);
    $this->options=array();
    $this->default=null;
  }

  public function opt($id,$elem,$o=NULL)
  {
    array_push($this->options,array($id,$elem,$o));
    return $this;
  }

  public function value($id)
  {
    $this->default=$id;
    return $this;
  }

  public function out()
  {
    $buf="<li class=\"".$this->class."\"><fieldset><legend>".$this->label."</legend><ul>";
    foreach($this->options as $o)
      {
	$buf.="<li><input type=\"radio\" name=\"".$this->name."\" id=\"".$this->name."__".$o[0]."\" value=\"".$o[0]."\" ";
	if($o[0]===$this->default)
	  {
	    $buf.="checked=\"checked\" ";
	  }
	$buf.="/><label for=\"".$this->name."__".$o[0]."\">".$o[1]."</label></li>";

	if($o[2]!==NULL)
	  {
	    $buf.=$o[2]->out();
	  }
      }
    $buf.="</ul></fieldset></li>";
    return $buf;
  }

  protected $options;
  protected $default;
}

////////////////////////////////////////////////////////////////////////////////

class FormSelect extends FormRadio
{
  public function opt($id,$elem=null,$o=null)
  {
    if(is_object($id))
      {
	array_push($this->options,$id);
      }
    else
      {
	array_push($this->options,array($id,$elem));
      }

    return $this;
  }

  public function out()
  {
    $buf="<li class=\"".$this->class."\"><label for=\"".$this->name."\">".$this->label."</label><select name=\"".$this->name."\" id=\"".$this->name."\">";
    foreach($this->options as $o)
      {
	if(is_object($o))
	  {
	    $o->value($this->default);
	    $buf.=$o->out();
	  }
	else
	  {
	    if($this->default===$o[0])
	      {
		$sel=" selected=\"selected\"";
	      }
	    else
	      {
		$sel='';
	      }
	    $buf.="<option value=\"".$o[0]."\"".$sel.">".$o[1]."</option>";
	  }
      }
    $buf.="</select></li>";
    return $buf;
  }
}

////////////////////////////////////////////////////////////////////////////////

class FormCheck extends A_FormField
{
  function __construct($n)
  {
    parent::__construct($n);
    $this->options=array();
    $this->default=array();
  }

  public function opt($id,$elem,$o=NULL)
  {
    array_push($this->options,array($id,$elem,$o));
    return $this;
  }

  public function value($id)
  {
    $this->default[$id]=true;
    return $this;
  }

  public function out()
  {
    $buf="<li class=\"".$this->class."\"><fieldset><legend>".$this->label."</legend><ul>";
    foreach($this->options as $o)
      {
	if(isset($this->default[$o[0]]))
	  {
	    $sel="checked=\"checked\" ";
	  }
	else
	  {
	    $sel="";
	  }

	$buf.="<li><input type=\"checkbox\" name=\"".$this->name."[]\" id=\""
	  .$this->name."__".$o[0]."\" value=\"".$o[0]."\" ".$sel."/>"
	  ."<label for=\"".$this->name."__".$o[0]."\">".$o[1]."</label></li>";

	if($o[2]!==NULL)
	  {
	    $buf.=$o[2]->out();
	  }
      }
    $buf.="</ul></fieldset></li>";
    return $buf;
  }
}

class FormFile extends A_FormField
{
  function __construct($n)
  {
    parent::__construct($n);
    $this->label=null;
  }

  function label($t)
  {
    $this->label=$t;
    return $this;
  }

  public function out()
  {
    return "<li class=\"".$this->class."\"><label for=\"".$this->name."\">".$this->label."</label><input type=\"file\" name=\"".$this->name."\" id=\"".$this->name."\" /></li>";
  }
}

////////////////////////////////////////////////////////////////////////////////

function f($type,$parm=null)
{
  switch($type)
    {
    case "text":
      return new FormText($parm);
    case "textarea":
      return new FormTextarea($parm);
    case "radio":
      return new FormRadio($parm);
    case "menu":
      return new FormSelect($parm);
    case "check":
      return new FormCheck($parm);
    case "fieldset":
      return new FormFieldset();
    case "optgroup":
      return new FormOptgroup($parm);
    case "msg":
      return new FormMessage($parm);
    case "placeholder":
      return new FormPlaceholder($parm);
    case "file":
      return new FormFile($parm);
    case "password":
      return new FormPassword($parm);
    case "form":
      return new Form($parm);
    }
}

?>
