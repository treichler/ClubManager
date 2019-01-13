<?php
// app/Controller/BlogsController.php
class BlogsController extends AppController
{

  public function beforeFilter() {
    // everybody is allowed to call 'index', 'view' and 'attachment'
    $this->Auth->allow('index', 'view', 'attachment', 'vote', 'news');
  }

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // users with privileg 'Blog create' are allowed to call 'add'
    if ($this->action === 'add' && array_has_key_val($privilegs['Privileg'], 'name', 'Blog create'))
      return true;

    // the creator and users with privileg 'Blog modify' are allowed to call 'edit'
    if ($this->action === 'edit') {
      $blogId = $this->request->params['pass'][0];
      if ($this->Blog->isOwnedBy($blogId, $user['id']) ||
          array_has_key_val($privilegs['Privileg'], 'name', 'Blog modify')) {
        return true;
      }
    }

    // the creator and users with privileg 'Blog delete' are allowed to call 'delete'
    if ($this->action === 'delete') {
      $blogId = $this->request->params['pass'][0];
      if ($this->Blog->isOwnedBy($blogId, $user['id']) ||
          array_has_key_val($privilegs['Privileg'], 'name', 'Blog delete')) {
        return true;
      }
    }
  }


  public $components = array('RequestHandler');

  public $helpers = array('Html', 'Form', 'Text', 'Js' => array('Jquery'));

  public function index()
  {
    if ($this->RequestHandler->isRss()) {
      $this->Blog->contain();
      $blogs = $this->Blog->find('all', array(
        'limit' => 20,
        'order' => array('Blog.expiry > NOW()' => 'desc', 'Blog.time_stamp' => 'desc', 'Blog.id' => 'desc')
      ));
      return $this->set(compact('blogs'));
    }

    $blogs_per_page = Configure::read('paginate.blog_count');

    $page = isset($this->params['url']['page']) ? $this->params['url']['page'] : 0;
    $offset = $blogs_per_page * $page;

    $this->Blog->contain('Tag');
    if(isset($this->params['url']['tag_id'])) {
      // find blogs by tag_id
      $tag_id = $this->params['url']['tag_id'];
      $blogs_tags = $this->Blog->BlogsTag->find('list', array(
        'conditions' => array('tag_id' => $tag_id),
        'fields' => array('blog_id')
      ));
      $blog_ids = [];
      foreach ($blogs_tags as $blog_tag) {
        $blog_ids[] = $blog_tag;
      }

      $count = $this->Blog->find('count', array(
        'conditions' => array('Blog.id' => $blog_ids)
      ));
      $pages = ceil($count / $blogs_per_page);
      $this->set(compact('pages'));

      $blogs = $this->Blog->find('all', array(
        'conditions' => array('Blog.id' => $blog_ids),
        'order' => array('Blog.expiry > NOW()' => 'desc', 'Blog.time_stamp' => 'desc', 'Blog.id' => 'desc'),
        'limit'  => $blogs_per_page,
        'offset' => $offset
      ));
      $this->set('tag_id', $tag_id);

      $this->set('foo', $count);
    } else {
      // blogs ordered on top until expiry date is reached
      // order blogs by expiry descending, followed by id descending
      $count = $this->Blog->find('count');
      $pages = ceil($count / $blogs_per_page);
      $this->set(compact('pages'));
      $blogs = $this->Blog->find('all', array(
        'order'  => array('Blog.expiry > NOW()' => 'desc', 'Blog.time_stamp' => 'desc', 'Blog.id' => 'desc'),
        'limit'  => $blogs_per_page,
        'offset' => $offset
      ));
    }

    if ($offset >= $count) $offset = $count - $blogs_per_page;
    $this->set(compact('page'));

    $this->set('blogs', $blogs);
    // get names of users who created at least one blog
    $this->set('user_names', $this->getUserNames($this->name));
  }

  public function news() {
    $count = 8;

    $this->loadModel('Gallery');
    $this->Blog->contain('Storage');
    $this->Gallery->contain();

    $blogs = $this->Blog->find('all', array(
      'order'  => array('Blog.expiry > NOW()' => 'desc', 'Blog.id' => 'desc'),
      'limit'  => $count
//      'limit'  => 4
    ));
    $galleries = $this->Gallery->find('all', array(
      'order'  => array('Gallery.id' => 'desc'),
      'limit' => $count
//      'limit' => 3
    ));

    $news = [];
    $now = new DateTime();
    $b = $g = 0;
    $blogs_count = count($blogs);
    $galleries_count = count($galleries);
    for ($i = 0; $i < $count; $i++) {

      // priority:
      // 1. blog with valid expiry
      // 2. newest date of creation
      if ($g < $galleries_count && $b < $blogs_count) {
        $expiry = new DateTime($blogs[$b]['Blog']['expiry']);
        $blog_created = new DateTime($blogs[$b]['Blog']['created']);
        $gallery_created = new DateTime($galleries[$g]['Gallery']['created']);
      }
      if ($b < $blogs_count &&                  // blogs still left
          isset($blogs[$b]['Storage']['id']) && // blog has picture
          ($g >= $galleries_count ||            // no more galleries left
           $expiry->getTimestamp() > $now->getTimestamp() ||
           $blog_created->getTimestamp() > $gallery_created->getTimestamp())) {
        $news[] = array(
          'type'      => 'Blog',
          'id'        => $blogs[$b]['Blog']['id'],
          'title'     => $blogs[$b]['Blog']['title'],
          'created'   => $blogs[$b]['Blog']['created'],
          'url'       => array('action' => 'view', $blogs[$b]['Blog']['id'])  ,
          'image_url' => array('action' => 'attachment', $blogs[$b]['Blog']['id'])  ,
          'user_id'   => $blogs[$b]['Blog']['user_id']
        );
        $b ++;
      } else {
        if ($g >= $galleries_count) break;      // no more galleries left
        $news[] = array(
          'type'      => 'Gallery',
          'id'        => $galleries[$g]['Gallery']['id'],
          'title'     => $galleries[$g]['Gallery']['title'],
          'created'   => $galleries[$g]['Gallery']['created'],
          'url'       => array('controller' => 'galleries', 'action' => 'view', $galleries[$g]['Gallery']['id'])  ,
          'image_url' => array('controller' => 'photos', 'action' => 'photo', $galleries[$g]['Gallery']['photo_id'])  ,
          'user_id'   => $galleries[$g]['Gallery']['user_id']
        );
        $g ++;
      }
    }
    $this->set(compact('news'));
    $this->set(compact('blogs'));
    $this->set(compact('galleries'));
  }

  public function view($id = null)
  {
    $this->Blog->id = $id;
    $this->Blog->contain('Comment', 'Storage', 'Tag');
    $blog = $this->Blog->read();
    $this->set(compact('blog'));

    if (empty($blog))
      $this->redirect(array('action' => 'index'));

    // prepare Open Graph Metadata
    $body_text = preg_replace('=\(.*?\)=is', '', $blog['Blog']['body']);
    $body_text = strip_tags($body_text);
    $body_text = CakeText::truncate($body_text, 250, array(
      'ending' => '...',
      'exact' => true,
      'html' => true,
    ));
    $open_graph_meta_data = array(
      'url' => Router::url(array('controller' => 'blogs', 'action' => 'view', $blog['Blog']['id']), true),
      'title' => h($blog['Blog']['title']),
      'description' => $body_text,
      'image' => Router::url(array('controller' => 'blogs', 'action' => 'attachment', $blog['Blog']['id']), true),
      'type' => 'website'
    );
    $this->set(compact('open_graph_meta_data'));

    // get all users who are in this view
    $this->Blog->User->contain('Profile');
    $user_ids = array(0 => $blog['Blog']['user_id']);
    foreach ($blog['Comment'] as $comment) {
      $user_ids[] = $comment['user_id'];
    }
    $users = $this->Blog->User->find('all', array(
      'conditions' => array('User.id' => $user_ids)
    ));
//    $this->set('users', Set::combine($users, '{n}.User.id', '{n}.User'));
    $user_data = [];
    foreach ($users as $user) {
      $user_data[$user['User']['id']]['name'] = isset($user['Profile']['id']) ?
        $user['Profile']['first_name'] . ' ' . $user['Profile']['last_name'] : $user['User']['username'];
      $user_data[$user['User']['id']]['email'] = $user['User']['email'];
      $user_data[$user['User']['id']]['profile_id'] = isset($user['Profile']['id']) ? $user['Profile']['id'] : 0;
    }
    $this->set('users', $user_data);
  }

  public function attachment($id = null)
  {
    $this->Blog->id = $id;
    $this->Blog->contain('Storage');
    $blog = $this->Blog->read();

    if (isset($blog['Storage']['file'])) {
      // needs cakephp v >= 2.3
      $this->response->type($blog['Storage']['extension']);
//      $this->response->header(array('Content-Type' => $photo['MarkedStorage']['type']));
      $this->response->file($blog['Storage']['file']['path'] . $blog['Storage']['uuid'],
        array(
          'download' => true,
          'name' => $blog['Storage']['name'] . '.' . $blog['Storage']['extension']
      ));
    } else {
      $this->response->file('img/default_blog_img.png', array('download' => true));
    }
    return $this->response;
  }

  public function vote() {
    $response = array();
    if ($this->request->is('post') && $this->request->is('ajax')) {
      $user = $this->getUser();
      if (isset($user['Vote']['id'])) {
        $this->request->data['Vote']['id'] = $user['Vote']['id'];
        $this->Blog->contain();
        $response = $this->Blog->vote($this->request->data);
      } else {
        $response = array('state' => 'false', 'message' => 'Sie müssen angemeldet sein, um eine Stimme abgeben zu können.');
      }
    }
    $this->response->type('json');
    $this->response->body(json_encode($response));
    return $this->response;
  }

// Standard control for calling and saving HABTM relation
  public function add()
  {
    if ($this->request->is('post'))
    {
      $this->request->data['Blog']['user_id'] = $this->Auth->user('id');

      // if empty, use "now" as default date stamp
      if (empty($this->request->data['Blog']['time_stamp']))
        $this->request->data['Blog']['time_stamp'] = date('Y-m-d H:i:s');

      // users with privileg 'Blog expiry' are allowed to set expiry
      if (!array_has_key_val($this->getUser()['Privileg'], 'name', 'Blog expiry'))
        $this->request->data['Blog']['expiry'] = date('Y-m-d H:i:s');

      $this->Blog->create();
      if ($this->Blog->save($this->request->data))
      {
        $this->Session->setFlash('Blog wurde gespeichert.', 'default', array('class' => 'success'));
//        $this->Session->setFlash(print_r($this->request->data));
        $this->redirect(array('action' => 'index'));
      }
      else
      {
        $this->Session->setFlash('Blog konnte nicht gespeichert werden.');
      }
    }
    // add all tags to the instant variable
    $tags = $this->Blog->Tag->find('list',array('fields'=>array('id','name')));
    $this->set(compact('tags'));
  }

  public function edit($id = null)
  {
    $this->Blog->id = $id;
    $this->Blog->contain('Tag');
    $blog = $this->Blog->read();
    // add all tags to the instant variable
    $tags = $this->Blog->Tag->find('list',array('fields'=>array('id','name')));
    $this->set(compact('tags'));
    if ($this->request->is('get'))
    {
      $this->request->data = $blog;
    }
    else
    {
      // users with privileg 'Blog expiry' are allowed to set expiry
      if (!array_has_key_val($this->getUser()['Privileg'], 'name', 'Blog expiry'))
        $this->request->data['Blog']['expiry'] = $blog['Blog']['expiry'];

      // keep existing storage
      $this->request->data['Blog']['storage_id'] = $blog['Blog']['storage_id'];

      if ($this->Blog->save($this->request->data))
      {
        $this->Session->setFlash('Blog wurde aktualisiert.', 'default', array('class' => 'success'));
//        $this->Session->setFlash('good. ' . $this->request->data['Blog']['file_resized']['type'], 'default', array('class' => 'success'));
//        $this->redirect(array('action' => 'index'));
        $this->redirect(array('action' => 'view', $blog['Blog']['id']));
      }
      else
      {
        $this->Session->setFlash('Blog konnte nicht aktualisiert werden.');
      }
    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Blog->delete($id)) {
      $this->Session->setFlash('Blog wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    } else {
      $this->Session->setFlash('Blog konnte nicht gelöscht werden.');
      $this->redirect(array('action' => 'view', $id));
    }
  }
}
