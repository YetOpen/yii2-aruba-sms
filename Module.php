<?php
namespace yetopen\smsaruba;

/**
 * Description of Module
 *
 * @author giorgia
 */
class Module extends \yii\base\Module {

    
    public function init() {
        $class = get_class($this);
        $reflector = new \ReflectionClass($class);

        $dir = dirname($reflector->getFileName());
        
        parent::init();
    }
}
