<?php
namespace ExamplesModule;

use Nette\Application\AppForm;

class TreeViewPresenter
extends \BasePresenter
{
	private $model;
	/** @persistent int */
	public $mode;

	protected function startup()
	{
		parent::startup();
		$this->model=new TreeViewModel;
	}

	public function renderTreeView($id=NULL)
	{
		if ($id!==NULL) {
			$this->invalidateControl('message');
			$this->template->site=$this->model->find($id);
			}
		$this->template->mode= $this->mode===NULL? 1 : $this->mode;
	}

	protected function createComponentPTree()
	{
		$tree=new \TreeView($this, 'pTree');
		$tree->useAjax=TRUE;
		$mode= NULL===$this->mode? 1 : $this->mode;
		$session=$this->getSession();
		$tree->mode=$mode;
		$tree->rememberState=TRUE;
		$tree->addLink('default', 'name', 'id', true, $this->presenter);
		$tree->dataSource=$this->model->getTree(true);

		$tree->renderer->wrappers['link']['collapse']='a class="ui-icon ui-icon-circlesmall-minus" style="float: left"';
		$tree->renderer->wrappers['link']['expand']='a class="ui-icon ui-icon-circlesmall-plus" style="float: left"';
		$tree->renderer->wrappers['node']['icon']='span class="ui-icon ui-icon-document" style="float: left"';

		return $tree;
	}

	public function handleMode($mode)
	{
		$this->invalidateControl('mode');
		$this['pTree']->invalidateControl();
	}

	public function renderCBTree()
	{
		$this->template->cTree=$this['frmCTree'];
	}

	public function createComponentFrmCTree()
	{
		$form=new AppForm($this, 'frmCTree');
		$tree=new \TreeView;
		$tree->addLink(NULL, 'name', 'id', TRUE, $this->presenter);
		$tree->dataSource=$this->model->getTree(TRUE);
		$form->addCBTree('ctree', 'tree', $tree);
		$form['ctree']->checkColumn='cb';
		$form->addSubmit('send', 'Send');
		$form->onSubmit[]=array($this, 'handleCTree');
		return $form;
	}

	public function handleCTree(AppForm $form)
	{
		$vals=$form->getValues();
		$this->model->setCTree($vals['ctree']);
	}
	
	protected function createComponentETree()
	{
		$tree=new \TreeView($this, 'eTree');
		$tree->addLink(null, 'name', 'id', TRUE, $this->presenter);
		$tree->dataSource=$this->model->getTree(TRUE);
		$et=new \EditableTree;
		$et->checkColumn='cb';
		$et->onChange='EChange';
		$et->onAdd='addItem';
		$et->onDel='delItem';
		$et->onCB='CB';
		$et->onEdit='Edit';
		$tree->setRenderer($et);
		return $tree;
	}

	public function handleEChange($eTree)
	{
		$this->model->setETree(\EditableTree::parseTree($eTree));
	}

	public function handleAddItem($id, $value='Nová položka')
	{
		if (isset($id) && !$id)
			$id=NULL;
		$this->model->addItem($id, $value);
		$this->flashMessage('Položka pridaná', 'info');
		
	}

	public function handleDelItem($id)
	{
		$this->model->delItem($id);
		$this->flashMessage('Položka (vrátane podpoložiek) zmazaná', 'info');
	}

	public function handleCB($tid, $vis)
	{
		$this->model->setCB($tid, $vis=='true');
	}

	public function handleEdit($id, $value=NULL)
	{
		if ($value==NULL || !strlen($v=trim($value)))
			$value='bezmena';
		$this->model->setName($id, $v);
		$this->invalidateControl($id);
		echo $value;
		$this->terminate();
	}
}