<fieldset>
	<legend>Register Your Account</legend>
		<form action="/api/register.php" method="POST">
			<ul>
				<?php 
					if (isset($_GET['e']) && intval($_GET['e'] == 1)) {
						echo '<div class="message warning">There was a problem creating the account. Perhaps try a different username</div>';
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
					<input type="submit" value="Register" />
				</li>
			</ul>
		</form>
</fieldset>