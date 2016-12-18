<div class="row">
<div class="row" id="registerStatus">
	
</div>
<form class="form-horizontal col-lg-offset-2 col-lg-8" id="registerNewAccount">
<fieldset class="well bs-component">
	<div class="form-group">
		<label for="inputEmail" class="col-lg-2 control-label">Email</label>
		<div class="col-lg-10">
			<input type="text" class="form-control" id="inputEmail" placeholder="Email" name="registerEmail">
			<label class="control-label bold" for="inputEmail" id="inputEmailStatus">&nbsp;</label>
		</div>
	</div>
	<div class="form-group">
		<label for="inputPassword" class="col-lg-2 control-label">Password</label>
		<div class="col-lg-10">
			<input type="password" class="form-control" id="inputPassword" placeholder="Password" name="registerPassword">
			<label class="control-label bold" for="inputPassword" id="inputPasswordStatus">&nbsp;</label>
		</div>
	</div>
	<div class="form-group">
		<label for="inputRetypePassword" class="col-lg-2 control-label">Retype Password</label>
		<div class="col-lg-10">
			<input type="password" class="form-control" id="inputRetypePassword" placeholder="Retype Password" name="registerRetypePassword">
			<label class="control-label bold" for="inputRetypePassword" id="inputRetypePasswordStatus">&nbsp;</label>
		</div>
	</div>
	<div class="form-group">
		<div class="checkbox">
		 	<label class="col-lg-12 control-label text-center">
		 		<input type="checkbox" name="registerRuleAccepted" id="registerRuleAccepted">
		 		<span id="registerRuleAcceptedStatus bold">
			 	By signing up, you agree to the <?php echo $_siteName?> Service Terms & Conditions and the Privacy Policy.
			 	</span>
			</label>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 float-right text-right">
			 <a class="btn btn-primary" id="submitNewAccount">Submit</a>
		</div>
	</div>
	<!--<div class="form-group">
		<div class="col-lg-10 col-lg-offset-2">
		  <button type="reset" class="btn btn-default">Cancel</button>
		  <button type="submit" class="btn btn-primary">Submit</button>
		</div>
	</div>-->
</fieldset> 
	</form>
</div>