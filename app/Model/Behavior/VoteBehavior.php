<?php
/**
 * Behavior for voting on models.
 *
 *
 * Usage:
 * Create a table named 'votes' with columns 'id' (primary key), 'user_id' (foreign
 * key that points to the user who does the voting) and for every table wich needs
 * to be voted add a column named '(table_name_lower_case_plural)_votes' (INT) to
 * count the votings.
 * Add the columns 'good', 'bad', 'sum' and 'median' to the tables you want to vote.
 * Create a join-table between every model you want to vote and the votes-table.
 * In the model to be voted and in the vote-model setup the 'hasAndBelongsToMany'
 * relation.
 * You can now vote by calling '(ModelToBeVoted)->vote($val)' where $val is +1 for
 * a positive vote and -1 for a negative vote.
 *
 *
 *---------------------------------------------------------------------------------------
 * Example:
 *---------------------------------------------------------------------------------------
 *
 * SQL:
 * CREATE TABLE users (
 *   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
 *   username VARCHAR(50), -- by CakePHP convention
 *   password VARCHAR(50), -- by CakePHP convention
 *   created DATETIME DEFAULT NULL,
 *   modified DATETIME DEFAULT NULL
 * )
 * CREATE TABLE blogs (
 *   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
 *   user_id INT(11) NOT NULL COMMENT 'Save the creator of the blog',
 *   title VARCHAR(50),
 *   body TEXT,
 *   good INT COMMENT 'Positive votes on the blog',
 *   bad INT COMMENT 'Negative votes on the blog',
 *   sum INT COMMENT 'Sum of all votes on the blog',
 *   median FLOAT COMMENT 'Median of all votes on the blog',
 *   created DATETIME DEFAULT NULL,
 *   modified DATETIME DEFAULT NULL
 * );
 *
 * CREATE TABLE votes (
 *   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
 *   user_id INT(11) NOT NULL COMMENT 'Connect the vote to the user',
 *   blog_votes INT COMMENT 'Count of votes on blogs',
 *   created DATETIME DEFAULT NULL,
 *   modified DATETIME DEFAULT NULL
 * );
 *
 * CREATE TABLE blogs_votes (
 *   blog_id INT(11) NOT NULL,
 *   vote_id INT(11) NOT NULL
 * );
 *
 *
 * app/Model/Vote.php:
 * <?php
 * class Vote extends AppModel {
 *   public $actsAs = array('Containable'); // this is optional
 *   public $belongsTo = array('User');
 * }
 *
 * app/Model/Blog.php:
 * <?php
 * class Blog extends AppModel {
 *   public $actsAs = array('Containable', 'Vote'); // 'Containable' is optional
 *   public $hasAndBelongsToMany = array('Vote');
 * }
 *
 */

App::uses('ModelBehavior', 'Model');

class VoteBehavior extends ModelBehavior {

/**
 * Types of relationships available for models
 *
 * @var array
 */
//  public $types = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');

/**
 * Runtime configuration for this behavior
 *
 * @var array
 */
//  public $runtime = array();

  public function vote(Model $Model, $data) {
    $id = $data[$Model->alias]['id'];
    $vote_id = $data['Vote']['id'];
    $val = $data[$Model->alias]['vote'];
    $join_table_name = Inflector::pluralize($Model->alias) . 'Vote';
    $join_table_this_id = strtolower($Model->alias) . '_id';

    // check if already voted -> return
    $join_table = $Model->{$join_table_name}->find('first', array(
      'conditions' => array($join_table_this_id => $id, 'vote_id' => $vote_id)
    ));
    if (!empty($join_table))
      return array('state' => 'false', 'message' => 'Bereits abgestimmt.');

    // check if val == 0 -> return
    if ($val == 0)
      return array('state' => 'false', 'message' => 'Es wurde keine Stimme abgegeben.');

    // get the semaphore
    $sem = sem_get(1001, 1);
    // busy waiting for the lock
    while (!sem_acquire($sem));
    // here starts the locked code

    $Model->id = $id;
    $table = $Model->read();

    // count up sum
    $table[$Model->alias]['sum']++;

    // if val > 0 count up good
    if ($val > 0)
      $table[$Model->alias]['good']++;

    // if val < 0 count up bad
    if ($val < 0)
      $table[$Model->alias]['bad']++;

    // calculate median
    $table[$Model->alias]['median'] = ($table[$Model->alias]['good'] - $table[$Model->alias]['bad']) / $table[$Model->alias]['sum'];

    $Model->create();
    if ($Model->save($table)) {
      $Model->{$join_table_name}->create();
      if ($Model->{$join_table_name}->save(array($join_table_this_id => $id, 'vote_id' => $vote_id))) {
        $Model->Vote->id = $vote_id;
        $vote = $Model->Vote->read();
        if ($vote['Vote'][Inflector::underscore($Model->alias) . '_votes']) {
          $vote['Vote'][Inflector::underscore($Model->alias) . '_votes']++;
        } else {
          $vote['Vote'][Inflector::underscore($Model->alias) . '_votes'] = 1;
        }
        $Model->Vote->create();
        if ($Model->Vote->save($vote)) {
          // release the lock
          sem_release($sem);
          return array('state' => 'true', 'data' => $table, 'message' => 'Stimme wurde gewertet.');
        }
      }
    }

    // release the lock
    sem_release($sem);

    return array('state' => 'false', 'message' => 'Stimme konnte nicht gespeichert werden.');
  }


}

