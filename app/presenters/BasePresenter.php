<?php
use Nette\Web,
	Nette\Application;

abstract class BasePresenter
extends Nette\Application\Presenter
{
	protected function beforeRender()
	{
		$this->template->bl=$this->getApplication()->storeRequest();
	}

	protected function createTemplate()
	{
		$template=parent::createTemplate();
		return $template;
	}
}
