<?php
//http://docs.phalconphp.com/zh/latest/reference/models.html#binding-parameters


//事件与事件管理器（Events and Events Manager）
Operation 	         Name 	                 Can stop operation? 	Explanation

Inserting/Updating 	 beforeValidation 	        YES 	Is executed before the fields are validated for not nulls/empty strings or foreign keys
Inserting 	         beforeValidationOnCreate 	YES 	Is executed before the fields are validated for not nulls/empty strings or foreign keys when an insertion operation is being made
Updating 	         beforeValidationOnUpdate 	YES 	Is executed before the fields are validated for not nulls/empty strings or foreign keys when an updating operation is being made
Inserting/Updating 	 onValidationFails 	        YES (already stopped) 	Is executed after an integrity validator fails
Inserting 	         afterValidationOnCreate 	YES 	Is executed after the fields are validated for not nulls/empty strings or foreign keys when an insertion operation is being made
Updating 	         afterValidationOnUpdate 	YES 	Is executed after the fields are validated for not nulls/empty strings or foreign keys when an updating operation is being made
Inserting/Updating 	 afterValidation 	        YES 	Is executed after the fields are validated for not nulls/empty strings or foreign keys
Inserting/Updating 	 beforeSave 	            YES 	Runs before the required operation over the database system
Updating 	         beforeUpdate 	            YES 	Runs before the required operation over the database system only when an updating operation is being made
Inserting 	         beforeCreate 	            YES 	Runs before the required operation over the database system only when an inserting operation is being made
Updating 	         afterUpdate 	            NO 	Runs after the required operation over the database system only when an updating operation is being made
Inserting 	         afterCreate 	            NO 	Runs after the required operation over the database system only when an inserting operation is being made
Inserting/Updating 	 afterSave 	                NO 	Runs after the required operation over the database system
Deleting 	         beforeDelete 	            YES 	Runs before the delete operation is made
Deleting 	         afterDelete 	            NO 	Runs after the delete operation was made

//例子
public function beforeSave()
{
    if($this->age < 20){
        $this->appendMessage(new \Phalcon\Mvc\Model\Message('xxxxx'));
        return false;
        //OR
        throw new \InvalidArgumentException('The age is too yang');
    }
}

//再说一下异常
Type 	                Description
PresenceOf 	            Generated when a field with a non-null attribute on the database is trying to insert/update a null value
ConstraintViolation 	Generated when a field part of a virtual foreign key is trying to insert/update a value that doesn’t exist in the referenced model
InvalidValue 	        Generated when a validator failed because of an invalid value
InvalidCreateAttempt 	Produced when a record is attempted to be created but it already exists
InvalidUpdateAttempt 	Produced when a record is attempted to be updated but it doesn’t exist

//初始化
public function initialize()
{
    $this->setSource("the_robots");//改变模型的表映射
}

//onConstruct 在 initialize 前面执行
public function onConstruct()
{
    //...
}

//改变模型的表映射
public function getSource()
{
    return "the_robots";
}
//或者这样
public function initialize()
{
    $this->setSource("the_robots");
}

//模型里使用自定义事件管理器（Using a custom Events Manager）
use Phalcon\Mvc\Model,
Phalcon\Events\Manager as EventsManager;

class Robots extends Model
{
    public function initialize()
    {
        $eventsManager = new EventsManager();

        //Attach an anonymous function as a listener for "model" events
        $eventsManager->attach('model', function($event, $robot) {
            if ($event->getType() == 'beforeSave') {
                if ($robot->name == 'Scooby Doo') {
                    echo "Scooby Doo isn't a robot!";
                    return false;
                }
            }
            return true;
        });
        //Attach the events manager to the event
        $this->setEventsManager($eventsManager);
    }
}

//或者在入口文件这样写
//Registering the modelsManager service
$di->setShared('modelsManager', function() {

    $eventsManager = new \Phalcon\Events\Manager();

    //Attach an anonymous function as a listener for "model" events
    $eventsManager->attach('model', function($event, $model){

        //Catch events produced by the Robots model
        if (get_class($model) == 'Robots') {

            if ($event->getType() == 'beforeSave') {
                if ($model->name == 'Scooby Doo') {
                    echo "Scooby Doo isn't a robot!";
                    return false;
                }
            }

        }
        return true;
    });

        //Setting a default EventsManager
        $modelsManager = new ModelsManager();
        $modelsManager->setEventsManager($eventsManager);
        return $modelsManager;
});

//验证数据完整性（Validating Data Integrity)
//更多请看 http://docs.phalconphp.com/zh/latest/reference/models.html#validating-data-integrity
use Phalcon\Mvc\Model\Validator\InclusionIn,
Phalcon\Mvc\Model\Validator\Uniqueness;

class Robots extends \Phalcon\Mvc\Model
{

    public function validation()
    {

        $this->validate(new InclusionIn(
            array(
                "field"  => "type",
                "domain" => array("Mechanical", "Virtual")
            )
        ));

        $this->validate(new Uniqueness(
            array(
                "field"   => "name",
                "message" => "The robot name must be unique"
            )
        ));

        return $this->validationHasFailed() != true;
    }
}