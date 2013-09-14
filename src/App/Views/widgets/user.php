<li <?php echo $currentUser ? 'class="current-user"' : ''; ?> id="user-<?php echo $this->e($user->id); ?>">   
    <div class="player-name"><?php echo $this->e($user->name); ?> (ip: <?php echo $this->e($user->ip); ?>)</div>
    <div class="both"></div>	
    <ul class="cards">  	
        <?php foreach($user->cards as $cardName => $opened) : ?>		
        <li class="card-container">
            <?php if ($currentUser): ?>
				<img class="active-user-card" data-card-opened="<?php echo $opened ? 1 : 0; ?>" data-active-user="<?php echo $this->e($user->id); ?>" data-card-name="<?php echo $this->e($cardName); ?>" src="<?php echo $this->url('cards_img');?>/<?php echo ($opened ? $cardName : 'Closed'); ?>.gif" />
                <img class="active-user-card" data-card-opened="<?php echo $opened ? 0 : 1; ?>" data-active-user="<?php echo $this->e($user->id); ?>" data-card-name="<?php echo $this->e($cardName); ?>" src="<?php echo $this->url('cards_img');?>/<?php echo ($opened ? 'Closed' : $cardName); ?>.gif" />				
            <?php else : ?>
				<img data-card-name="<?php echo $this->e($cardName);  ?>" data-card-opened="<?php echo $opened ? 1 : 0; ?>" src="<?php echo $this->url('cards_img');?>/<?php echo $opened ? $cardName : 'Closed'; ?>.gif" />			
                <img data-card-name="<?php echo $this->e($cardName);  ?>" data-card-opened="<?php echo $opened ? 0 : 1; ?>" src="<?php echo $this->url('cards_img');?>/<?php echo $opened ? 'Closed' : $cardName; ?>.gif" />  
            <?php endif; ?>
        </li>
        <?php endforeach; ?>   
        
    </ul>
    <div class="both"></div>
</li>
