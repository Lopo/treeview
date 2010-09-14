<?php
use Nette\Forms\Form;
Form::extensionMethod('Nette\Forms\Form::addCBTree', array('CBTree', 'addCBTree'));
Form::extensionMethod('Nette\Forms\Form::addPswdInput', array('PswdInput', 'addPswdInput'));
Form::extensionMethod('Nette\Forms\Form::addCBox3S', array('CBox3S', 'addCBox3S'));