<!-- File: /app/View/Blogs/index.ctp -->

<?php // This file contains PHP ?>

<h1>Aktuelles</h1>

<?php if ($this->Html->hasPrivileg($this_user, array('Blog create'))): ?>
  <p><?php echo $this->Html->link('Neuer Blog', array('controller' => 'blogs', 'action' => 'add')); ?></p>
<?php endif; ?>

<?php foreach ($blogs as $blog): ?>
<article class="entry clearfix">

  <div class="entry-body">
    <h1 class="title"><?php
      echo $this->Html->link(h($blog['Blog']['title']), array('controller' => 'blogs', 'action' => 'view', $blog['Blog']['id']));
    ?></h1>
    <?php
      $bodyText = strip_tags($blog['Blog']['body']);
      $bodyText = $this->Text->truncate($bodyText, 400, array(
        'ending' => '...',
        'exact' => true,
        'html' => true,
      ));
      echo $bodyText;
    ?>
    <?php echo $this->Html->link('weiterlesen', array('controller' => 'blogs', 'action' => 'view', $blog['Blog']['id'])); ?>
  </div><!-- end .entry-body -->

  <div class="entry-meta">
    <ul>
      <li>
        <?php
          $href = Router::url(array('controller' => 'blogs', 'action' => 'view', $blog['Blog']['id']), true);
        ?><a href="<?php echo $href; ?>" title="<?php echo h($blog['Blog']['title']); ?>" rel="bookmark">
          <span class="post-format ">Permalink</span>
        </a>
      </li>
      <li>
        <span class="title">Autor:</span>
        <?php echo h($user_names[$blog['Blog']['user_id']]); ?> 
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
    </ul>
  </div><!-- end .entry-meta -->
</article><!-- end .entry -->
<?php endforeach; ?>
<?php unset($blog); ?>

<ul class="pagination">
<!--
  <li class="next"><a href="#">&larr; Next</a></li>
-->
<?php for ($i = 0; $i < $pages; $i++): ?>
  <?php if ($i == $page): ?>
    <li class="current"><?php echo $i + 1; ?></li>
  <?php else:
    if (isset($tag_id))
      $url_params = array('page' => $i, 'tag_id' => $tag_id);
    else
      $url_params = array('page' => $i);
  ?>
    <li><?php echo $this->Html->link($i+1, array('action' => 'index', '?' => $url_params)); ?></li>
  <?php endif; ?>
<?php endfor; ?>
<!--
  <li class="prev"><a href="#">Previous &rarr;</a></li>
-->
</ul>

