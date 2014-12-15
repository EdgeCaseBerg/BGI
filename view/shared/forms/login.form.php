<fieldset>
	<legend>Login to your Account</legend>
		<form action="/api/login.php" method="POST">
			<ul>
				<?php 
					if (isset($_GET['e']) && intval($_GET['e']) == 1) {
						echo '<div class="flakes-message warning">There was a problem logging into your account.</div>';
					}
					if (isset($_GET['s']) && intval($_GET['s']) == 1) {
						echo '<div class="flakes-message information">Login success!, redirecting you in 2 seconds</div>';
						echo '<META http-equiv="refresh" content="2;URL=/home" />';
					}
				?>
				<li>
					<label>Username</label>
					<input placeholder="Username" name="nickname" value="" type="text" size="32" maxlength="32"/>
				</li>
				<li>
					<label>Password</label>
					<input placeholder="Password" name="ident" value="" type="password" size="32" maxlength="32" />				
				</li>
				<li>
					<input type="submit" value="Login" />
				</li>
			</ul>
		</form>
</fieldset>