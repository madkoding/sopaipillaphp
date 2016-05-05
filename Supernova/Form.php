<?php

namespace Supernova;

/**
 * @category   Form
 * @package    Helpers
 * @author     José Luis Gonzalez <joseluis@supernovaphp.com>
 * @copyright  2014 Supernova Framework
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 3.0
 * @example
 *
 * $form = \Supernova\Form::create($modelName, $ValuesObject)
 * ->setModel($modelName)
 * ->setFields($FieldsArray)
 * ->setValues($ValuesObject)
 * ->setAction(array("controller" => "tests", "action" => "add"))
 * ->render();
 */

//class Form extends \Supernova\Model
class Form
{
    /**
     * HTML Output
     * @var string
     */
    private $output;

    /**
     * Default fields that not show in the form
     * @var array
     */
    public $notShow = array(
        "created",
        "updated",
        "created_at",
        "updated_at",
        "modified",
        "creado_en",
        "actualizado_en",
        "creado",
        "actualizado"
    );

    /**
     * Form parameters
     * @var array
     */
    private $params = array(
        "values" => array(),
        "fields" => array(),
        "action" => "",
        "submit" => "Submit",
        "schema" => array(),
        "modelName" => "",
        "modelForm" => ""
    );

    /**
     * Initialize form object
     * @param string $modelName Model name
     * @param array  $values    Values from model
     */
    public function __construct($modelName, $values = array())
    {
        $this->params['modelName'] = \Supernova\Inflector\Singularize::word($modelName);
        $this->params['modelForm'] = "\App\Model\\".$this->params['modelName']."Form";
        $this->params['schema'] = \Supernova\Cache::load($this->params['modelName']);
        $this->setValues($values);
        $this->setFields();
        $this->setAction();
        $this->setSubmit();
    }

    /**
     * Set values for each field in form
     * @param string $values Value
     */
    public function setValues($values)
    {
        $this->params['values'] = (is_object($values)) ? $values->toArray() : $values;
        return $this;
    }

    /**
     * Set fields to show in form
     * @param array $fields Fields
     */
    public function setFields($fields = array())
    {
        if (empty($fields)) {
            $vars = get_class_vars($this->params['modelForm']);
            $fields = $vars['schema'];
        }
        
        foreach ($fields as $field => $schema) {
            $this->injectFields($field);
        }
        return $this;
    }

    /**
     * Inject fields parameters in input
     * @param  string $field Field name
     * @return null
     */
    private function injectFields($field)
    {
        if (!in_array($field, $this->notShow)) {
            $values = (isset($this->params['values'][$field])) ? $this->params['values'][$field] : "";
            $input = new \Supernova\Input($this->params['modelForm'], $field, $values);
            $this->params['fields'][$field] = $input->render();
        }
    }
    
    /**
     * Unset fields from form
     * @param  array  $fields Fields
     * @return object         Form object
     */
    public function unsetFields($fields = array())
    {
        foreach ($fields as $field) {
            unset($this->params['fields'][$field]);
        }
        return $this;
    }

    /**
     * Set actions for form
     * @param string $action Action name
     */
    public function setAction($action = "")
    {
        $this->params['action'] = \Supernova\Route\Generate::url($action);
        return $this;
    }

    /**
     * Set submit button for form
     * @param string $submit Submit value name
     */
    public function setSubmit($submit = "Submit")
    {
        $this->params['submit'] = __($submit);
        return $this;
    }
    
    /**
     * Create output for form
     * @return string HTML form
     */
    public function createOutput()
    {
        $this->output = "<form class='form-horizontal' role='form' ";
        $this->output.= "action='".$this->params['action']."' method='POST' >";
        $this->output.= implode("\n", $this->params['fields']);
        $this->output.= '<div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <button type="submit" class="btn btn-default">'.$this->params['submit'].'</button>
            </div>
        </div>';
        $this->output.= '</form>';
    }

    /**
     * Show HTML Form
     * @return string Show HTML Form
     */
    public function render()
    {
        $this->createOutput();
        return $this->output;
    }

    /**
     * Create new form
     * @param  array $args   Parameters
     * @param  array $values Values
     * @return object        New form object
     */
    public static function create($args, $values = array())
    {
        $form = new \Supernova\Form($args, $values);
        return $form;
    }
}
