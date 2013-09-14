<h2>Игра <?php echo $this->e($currentUser->gameId); ?></h2>

<ul id="table">
<?php foreach ($users as $user) : ?>
    <?php echo $this->render('widgets/user', array('user' => $user, 'currentUser' => $user->id == $currentUser->id)); ?>
<?php endforeach; ?>
</ul>

<div class="doc-section clearfix">
    <a class="btn" href="<?php echo $this->url('index'); ?>">Новая игра</a>
    <a class="btn" id="log-button" href="<?php echo $this->url('games_log'); ?>">События</a>
</div>

<div class="log" id="log"></div>

<script src="<?php echo $this->url('js_assets');?>/jquery-1.9.1.js"></script>
<script src="<?php echo $this->url('js_assets'); ?>/game.js?"></script>
<script type="text/javascript">    
    new Game({
        'update_url'     : '<?php echo $this->url('games_update'); ?>',
        'leave_url'      : '<?php echo $this->url('games_leave'); ?>',
        'turn_url'       : '<?php echo $this->url('games_turn'); ?>',
        'img_url'        : '<?php echo $this->url('cards_img'); ?>',
        'users'          :  <?php echo json_encode($users); ?>,
        'current_user_id':  <?php echo $currentUser->id; ?>,
		'last_event_id'  :  <?php echo $log->getLastEventId(); ?>
    }).start();
</script>