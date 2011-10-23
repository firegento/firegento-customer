var validator = Validation.methods['validate-password'];
validator.test = function(v) {
	var pass=v.strip(); /*strip leading and trailing spaces*/
	return !(pass.length>0 && pass.length < 9);
};
validator.error = 'Please enter 9 or more characters. Leading or trailing spaces will be ignored.';