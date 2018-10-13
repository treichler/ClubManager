<?php
// app/Controller/BooksController.php
class BooksController extends AppController {

  public function isAuthorized($user) {
    $privilegs = $this->getUser();

    // club members are allowed to call 'index' and 'view'
    if (($this->action === 'index' || $this->action === 'view') && $this->isClubMember())
      return true;

    // users with privileg 'Music book' are allowed to call 'add', 'edit' and 'delete'
    if (($this->action === 'add' || $this->action === 'content' || $this->action === 'edit' || $this->action === 'delete') &&
        array_has_key_val($privilegs['Privileg'], 'name', 'Music book'))
      return true;
  }


  public $helpers = array('Html', 'Form');


  public function index() {
    $this->set('books', $this->Book->find('all'));
  }

  public function view($id = null) {
    $this->Book->contain();
    $this->Book->id = $id;
    $this->set('book', $this->Book->read());
    // fetch book's sheets
    $this->Book->Sheet->contain();
    $sheets = $this->Book->Sheet->find('all', array(
      'conditions' => array('Sheet.book_id' => $id),
      'order'      => array('Sheet.page' => 'asc')
    ));
    $this->set(compact('sheets'));

    $musicsheet_ids = array();
    foreach ($sheets as $sheet) {
      $musicsheet_ids[] = $sheet['Sheet']['musicsheet_id'];
    }
    $this->Book->Sheet->Musicsheet->contain('Composer', 'Arranger', 'Publisher');
    $tmp = $this->Book->Sheet->Musicsheet->find('all', array(
      'conditions' => array('Musicsheet.id' => $musicsheet_ids)
    ));
    $musicsheets = array();
    foreach ($tmp as $musicsheet) {
      $musicsheets[$musicsheet['Musicsheet']['id']] = $musicsheet;
    }
    $this->set(compact('musicsheets'));
  }

  public function add() {
    if ($this->request->is('post')) {
      $this->Book->create();
      if ($this->Book->save($this->request->data)) {
        $this->Session->setFlash('Buch/Mappe wurde gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Buch/Mappe konnte nicht gespeichert werden.');
      }
    }
  }

  public function content($id = null) {
    if ($this->request->is('post')) {
      $id = $this->request->data['Book']['id'];
      $this->Book->create();
      if ($this->Book->saveAssociated($this->request->data, array('validate' => 'first'))) {
        $this->Session->setFlash('Seiten wurden gespeichert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'view', $id));
      } else {
        $new_sheets = [];
        foreach ($this->request->data['Sheet'] as $sheet) {
          if (!isset($sheet['id']))
            $new_sheets[] = array('Sheet' => $sheet);
        }
        $this->set(compact('new_sheets'));
        $this->Session->setFlash('Seiten konnten nicht gespeichert werden.');
      }
    }

    $this->Book->contain();
    $this->Book->id = $id;
    $this->set('book', $this->Book->read());
    // fetch book's sheets
    $this->Book->Sheet->contain('Book', 'Musicsheet');
    $sheets = $this->Book->Sheet->find('all', array(
      'conditions' => array('Sheet.book_id' => $id),
      'order'      => array('Sheet.page' => 'asc')
    ));
    $this->set(compact('sheets'));
    // add all musicsheets to the instant variable
    $this->Book->Sheet->Musicsheet->contain('Publisher');
    $tmp = $this->Book->Sheet->Musicsheet->find('all', array(
      'order'  => array('title' => 'asc')
    ));
    $musicsheets = [];
    foreach($tmp as $musicsheet) {
      $musicsheets[$musicsheet['Musicsheet']['id']] = $musicsheet['Musicsheet']['title'] . ' (' .
                                                      $musicsheet['Publisher']['name']. ')';
    }
    $this->set(compact('musicsheets'));
  }

  public function edit($id = null) {
    if ($this->request->is('get')) {
      $this->Book->id = $id;
      $this->request->data = $this->Book->read();
    } else {
      $this->Book->create();
      if ($this->Book->save($this->request->data)) {
        $this->Session->setFlash('Buch/Mappe wurde aktualisiert.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('Buch/Mappe konnte nicht gespeichert werden.');
      }
    }
  }

  public function delete($id) {
    if ($this->request->is('get')) {
      throw new MethodNotAllowedException();
    }
    if ($this->Book->delete($id)) {
      $this->Session->setFlash('Buch/Mappe mit ID: ' . $id . ' wurde gelöscht.', 'default', array('class' => 'info'));
      $this->redirect(array('action' => 'index'));
    }
  }

}

