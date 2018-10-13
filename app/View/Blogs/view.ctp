<!-- File: /app/View/Blogs/view.ctp -->

<?php // This file contains PHP ?>

<?php echo $this->Html->script('voting'); ?>

<h1><?php echo h($blog['Blog']['title']); ?></h1>

<article class="entry single clearfix">

  <a title="<?php echo h($blog['Blog']['title']); ?>">
    <img class="entry-image" src="<?php echo Router::url(array('controller' => 'blogs', 'action' => 'attachment', $blog['Blog']['id']), true); ?>" />
  </a>

  <div class="entry-body">
    <?php if (isset($blog['Storage']['id'])): ?>
      <h1 class="title"><?php echo h($blog['Blog']['title']); ?></h1>
    <?php endif; ?>

    <?php echo $blog['Blog']['body']; ?>
  </div><!-- end .entry-body -->

  <div class="entry-meta">
    <ul>
<!--
      <li>
        <a href="<?php echo Router::url(array('controller' => 'blogs', 'action' => 'view', $blog['Blog']['id']), true); ?>" title="<?php echo h($blog['Blog']['title']); ?>" rel="bookmark">
          <span class="post-format ">Permalink</span>
        </a>
      </li>
-->
      <li>
      </li>

      <li>
        <div class="avatar" gravatar="<?php echo md5(strtolower(trim($users[$blog['Blog']['user_id']]['email']))) ?>">
          <img <?php if (isset($users[$blog['Blog']['user_id']]['profile_id']))
            echo 'src="' . Router::url(array('controller' => 'profiles', 'action' => 'attachment',
                            $users[$blog['Blog']['user_id']]['profile_id']), true) . '"';
          ?> />
        </div>
      </li>

      <li>
        <span class="title">Autor:</span>
        <?php echo h($users[$blog['Blog']['user_id']]['name']); ?>
      </li>
      <li>
        <span class="title">Datum:</span>
        <?php echo $this->Html->getDate($blog['Blog']['time_stamp'], array('year' => 'true', 'day' => true)); ?>
      </li>
<?php if (count($blog['Tag'])): ?>
      <li>
        <span class="title">Tags:</span>
        <?php
          $tags = [];
          foreach ($blog['Tag'] as $tag) {
            $tags[] = $this->Html->link($tag['name'], array(
              'controller' => 'blogs', 'action' => 'index', "?" => array('tag_id' => $tag['id'])
            ));
          }
          echo implode(', ', $tags);
        ?>
      </li>
<?php endif; ?>
<?php if ($blog['Blog']['comment_count']): ?>
      <li>
        <span class="title">Kommentare:</span>
        <a href="#comments"><?php echo $blog['Blog']['comment_count']; ?></a>
      </li>
<?php endif; ?>
<?php if ($blog['Blog']['median']): ?>
      <li>
        <span class="title">Bewertung:</span>
        <a id="VoteMedian"><?php echo $blog['Blog']['median'] ?></a>
      </li>
<?php endif; ?>
<?php if ($blog['Blog']['sum']): ?>
      <li>
        <span class="title">Relevanz:</span>
        <a id="VoteSum"><?php echo $blog['Blog']['sum'] ?></a>
      </li>
<?php endif; ?>
<?php if (!empty($this_user['User']['id'])): ?>
      <li>
        <span class="title">Bewerten:</span>
        <?php
          echo $this->Html->link('gut', 'javascript:void(0)',
          array('onclick' => 'vote("' . Router::url(array('controller' => 'blogs', 'action' => 'vote'), true) .
                '",' . $blog['Blog']['id'] . ',1);'));
          echo ' | ';
          echo $this->Html->link('schlecht', 'javascript:void(0)',
          array('onclick' => 'vote("' . Router::url(array('controller' => 'blogs', 'action' => 'vote'), true) .
                '",' . $blog['Blog']['id'] . ',-1);'));
        ?>
      </li>
<?php endif; ?>
<?php if ($this->Html->hasPrivileg($this_user, array('Blog create', 'Blog modify', 'Blog delete'))): ?>
      <li>
        <?php
          echo $this->Html->link('bearbeiten', array('controller' => 'blogs', 'action' => 'edit', $blog['Blog']['id']));
          echo ' ';
          echo $this->Form->postLink('löschen',
          array('action' => 'delete', $blog['Blog']['id']),
          array('confirm' => 'Soll der Blog "' .
                $blog['Blog']['title'] . '" tatsächlich gelöscht werden?'));
        ?>
      </li>
<?php endif; ?>
    </ul>
  </div><!-- end .entry-meta -->
</article><!-- end .entry -->


<?php if ($blog['Blog']['comment_count']): ?>
<section id="comments">
  <?php if ($blog['Blog']['comment_count'] > 1): ?>
  <h6 class="section-title">Kommentare (<?php echo $blog['Blog']['comment_count']; ?>)</h6>
  <?php else: ?>
  <h6 class="section-title">Kommentar</h6>
  <?php endif; ?>

  <ol class="comments-list">
  <?php foreach ($blog['Comment'] as $comment): ?>
    <li class="comment">
<article>
      <div class="avatar" gravatar="<?php echo md5(strtolower(trim($users[$comment['user_id']]['email']))) ?>">
        <img <?php 
          if (isset($users[$comment['user_id']]['profile_id']))
            echo 'src="' . Router::url(array('controller' => 'profiles', 'action' => 'attachment',
                            $users[$comment['user_id']]['profile_id']), true) . '"';
        ?> />
      </div>
      <div class="comment-meta">
        <h5 class="author"><a><?php echo h($users[$comment['user_id']]['name']); ?></a></h5>
        <p class="date">
          <?php echo $this->Html->getDateTime($comment['created']); ?>
          <?php if (!empty($this_user['User']['id']) && $this_user['User']['id'] == $comment['user_id']): ?>
            <?php echo $this->Html->link('bearbeiten', array('controller' => 'comments', 'action' => 'edit', $comment['id'])); ?>
            <?php // echo $this->Form->postLink('löschen',
                  // array('controller' => 'comments', 'action' => 'delete', $comment['id']),
                  // array('confirm' => 'Soll der Kommentar tatsächlich gelöscht werden?')); ?>
          <?php endif; ?>
        </p>
      </div><!-- end .comment-meta -->
      <div class="comment-body">
        <p><?php echo h($comment['body']); ?></p>
      </div><!-- end .comment-body -->
</article>
    </li>
  <?php endforeach; ?>
  <?php unset($comment); ?>
  </ol>
</section>
<?php endif; ?>


<section id="respond">
<?php if (empty($this_user['User']['id'])): ?>
  <p>Um einen Kommentar verfassen zu k&ouml;nnen, m&uuml;ssen Sie angemeldet sein</p>
<?php else: ?>
  <h6 class="section-title">Kommentar verfassen</h6>
  <p class="textarea-block">
    <?php
      echo $this->Form->create('Comment', array('action' => 'add', $blog['Blog']['id']));
      echo $this->Form->input('blog_id', array('type' => 'hidden', 'default' => $blog['Blog']['id']));
      echo $this->Form->input('body', array('label' => 'Kommentar'));
    //  echo $this->Form->input('id', array('type' => 'hidden'));
    ?>
  </p>
  <?php
    echo $this->Form->end('Speichern');
  ?>
  <div class="clear"></div>
<?php endif; ?>
</section>


<script type="text/javascript">

$(document).ready(function() {
  var size = $('.avatar').css('height');
  if (!size) size = '60px';

  $(".avatar img").error(function () {
    var src = "http://www.gravatar.com/avatar/" + $(this).parent().attr('gravatar') + "?d=mm&s=" + size
    var img = $(this).unbind("error");
    img.attr("src", src);
    img.attr("class", "gravatar")
  });
});

</script>

