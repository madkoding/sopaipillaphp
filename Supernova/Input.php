<?php
namespace Supernova;

/**
 * Usage:
 * $input = new \Supernova\Input($modelFormName, $field, $value);
 * $input->render();
 */
class Input extends \Supernova\Form
{
    /**
     * Store input value
     * @var string
     */
    private $value = "";

    /**
     * Check if input options include empty
     * @var boolean
     */
    private $emptyOption = false;

    /**
     * Options for input
     * @var array
     */
    private $options = array();

    /**
     * Input parameters
     * @var array
     */
    private $params = array(
        "id" => "_form",
        "name" => "data",
        "class" => "form-control",
        "type" => "text"
    );

    /**
     * Setting for each input
     * @var array
     */
    private $settings = array(
        "varchar" => "createInput",
        "select" => "createSelect",
        "int" => "createSelect",
        "text" => "createTextArea",
        "textarea" => "createTextArea",
        "checkbox" => "createInput",
        "tinyint" => "createInput",
        "bool" => "createInput",
        "boolean" => "createInput",
        "radio" => "createInput"
    );
    
    /**
     * Initialize input
     * @param string $modelFormName ModelForm name
     * @param string $field         Field name
     * @param string $value         Field value
     */
    public function __construct($modelFormName, $field = "", $value = "")
    {
        $modelForm = new $modelFormName();
        $this->params['id'] = $field."_form";
        $this->params['label'] = $field;

        if (isset($modelForm->schema[$field]['label'])) {
            if ($field == "id") {
                $this->params['label'] = "";
            } else {
                $this->params['label'] = $modelForm->schema[$field]['label'];
            }
        }

        if (isset($modelForm->schema[$field]['class'])) {
            $this->params['class'] = $modelForm->schema[$field]['class'];
        }

        if (isset($modelForm->schema[$field]['type'])) {
            $type = explode("(", $modelForm->schema[$field]['type']);
            $type = current($type);
            $this->params['type'] = $type;
        } else {
            if ($modelForm->schema[$field]['type'] == "PRI") {
                $this->params['type'] = "hidden";
            }
        }
        
        if (isset($modelForm->schema[$field]["empty"])) {
            if (!empty($modelForm->schema[$field]["empty"])) {
                $this->emptyOption = $modelForm->schema[$field]["empty"];
            }
        }

        if (isset($modelForm->schema[$field]["options"])) {
            if ($this->options = $modelForm->schema[$field]["options"]) {
                if (is_string($this->options)) {
                    $fn = explode("::", $modelForm->schema[$field]["options"]);
                    $fn[1] = str_replace("()", "", $fn[1]);
                    if (method_exists($fn[0], $fn[1])) {
                        $this->options = $fn[0]::$fn[1]();
                    }
                }
            }
        }

        $modelName = explode("\\", get_parent_class($modelForm));
        $modelName = end($modelName);
        
        $this->params['name'] = "data[$modelName][".$field."]";
        $this->value = $value;
        return $this;
    }

    /**
     * Set label for input
     * @param string $label Label name
     */
    public function setLabel($label = "")
    {
        $this->params['label'] = $label;
    }

    /**
     * Set type for input
     * @param string $type Type name
     */
    public function setType($type = "")
    {
        $this->params['type'] = $type;
    }

    /**
     * Set class for input
     * @param string $class Class names
     */
    public function setClass($class = "form-control")
    {
        $this->params['class'] = $class;
    }

    /**
     * Parse params into input
     * @return string Parsed params
     */
    private function printParams()
    {
        $params = "";
        foreach ($this->params as $param => $value) {
            $params.= $param."='".$value."' ";
        }
        return $params;
    }

    /**
     * Create new input
     * @return string HTML Input
     */
    private function createInput()
    {
        return "<input ".$this->printParams()." value='".$this->value."' />";
    }

    /**
     * Create new text area
     * @return string HTML TextArea
     */
    private function createTextArea()
    {
        return "<textarea ".$this->printParams().">".$this->value."</textarea>";
    }

    /**
     * Create new select
     * @return string HTML Select
     */
    private function createSelect()
    {
        $render = "<select ".$this->printParams()." value='".$this->value."' />";
        $render.= $this->createOptions();
        $render.= "</select>";
        return $render;
    }

    /**
     * Create new options for select
     * @return string Options for select
     */
    private function createOptions()
    {
        if ($this->emptyOption !== false) {
            array_unshift($this->options, array('' => "-- ".__($this->emptyOption)." --"));
        }

        foreach ($this->options as $k => $v) {
            $selected = ($k == $this->value) ? "selected" : "";
            $options[] = "<option value='$k' $selected>$v</option>";
        }
        return implode("\n", $options);
    }

    /**
     * Render input
     * @return string HTML Input
     */
    public function render()
    {
        $render ='<div class="form-group">';
        $render.= "<label for='".$this->params['id']."' class='col-sm-3 control-label'>";
        $render.= $this->params['label'];
        $render.= "</label>";
        $render.= '<div class="col-sm-9">';
        if (array_key_exists($this->params['type'], $this->settings)) {
            $functionName = $this->settings[$this->params['type']];
            $render.= $this->{$functionName}();
        } else {
            $render.= $this->createInput();
        }
        $render.= '</div></div>';
        return $render;
    }
}
