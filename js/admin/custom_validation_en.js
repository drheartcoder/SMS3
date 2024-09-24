
/*EXTRA VALIDATIONSF FOR JQUEY*/
jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Letters only please"); 

jQuery.validator.addMethod("alphanumeric", function(value, element) {
  return this.optional(element) || /^\w+$/i.test(value);
}, "Letters and numbers only"); 


jQuery.validator.addMethod("lettersWithSpace", function(value, element) {
  return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "Letters only");   
/*EXTRA VALIDATIONSF FOR JQUEY*/