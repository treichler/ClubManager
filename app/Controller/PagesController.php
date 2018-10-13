<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

    // number of pictures to be feched from groups and blogs
    $images_max = Configure::read('CMSystem.start_page_pictures_number');
    // number of pictures to be feched from groups
    $groups_limit = Configure::read('CMSystem.start_page_groups_limit');

    // try to get all groups with pictures except officials
    $this->loadModel('Group');
    $this->Group->contain('Kind');
    $groups_with_pics = $this->Group->find('all', array(
      'conditions' => array('Group.storage_id >=' => 1, 'Kind.is_public' => true, 'Kind.is_official' => false)
    ));
    if (count($groups_with_pics) > $groups_limit) {
      $groups = array($groups_with_pics[0], $groups_with_pics[rand(1, count($groups_with_pics) - 1)]);
      $blogs_limit = $images_max - $groups_limit;
    } else {
      $groups = $groups_with_pics;
      $blogs_limit = $images_max - count($groups_with_pics);
    }
    // try to get 'blogs_limit' blogs with pictures
    $this->loadModel('Blog');
    $this->Blog->contain();
    $blogs = $this->Blog->find('all', array(
      'conditions' => array('Blog.storage_id >=' => 1),
      'order'      => array('Blog.expiry > NOW()' => 'desc', 'Blog.time_stamp' => 'desc', 'Blog.id' => 'desc'),
      'limit'      => $blogs_limit
    ));

    // get upcoming events
    $this->loadModel('Event');
    $this->Event->contain('Group');
    $modes = $this->Event->Mode->findAllByIsPublic(true);
    $mode_ids = [];
    foreach ($modes as $mode) {
      $mode_ids[] = $mode['Mode']['id'];
    }
    $events = $this->Event->find('all', array(
      'conditions' => array(
        'Event.stop >=' => date('Y-m-d H:i:s'),
//        'Event.start <=' => date('Y-m-d', strtotime('+1 months')),
        'Event.mode_id' => $mode_ids
      ),
      'order'      => array('start' => 'asc'),
      'limit'      => 3
    ));

		$this->set(compact('page', 'subpage', 'title_for_layout', 'blogs', 'groups', 'events'));
		$this->render(implode('/', $path));
	}
}
