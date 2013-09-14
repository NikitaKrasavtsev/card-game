<div class="four columns alpha">
	<form action="<?php echo $this->url('index'); ?>/" method="POST">
		<?php if (isset($errors)) : ?>
			<?php echo $this->render('widgets/formErrors', array('errors' => $errors)); ?>
		<?php endif; ?>
		<div class="form-field">
			<label for="user-name">Ключ игры</label>
			<input id="user-name" name="user[gameId]" type="text" value="<?php $this->e($user->gameId);?>"/>
		</div>    
		<div class="form-field">
			<label for="user-name">Имя</label>
			<input id="user-name" name="user[name]" type="text" value="<?php $this->e($user->name);?>"/>
		</div>
		<div class="form-controls">
			<input class="btn" type="submit" value="Войти"/>
		</div>
	</form>
</div>