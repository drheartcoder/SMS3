
/*EXTRA VALIDATIONSF FOR JQUEY*/
jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Brieven allinich"); 

jQuery.validator.addMethod("alphanumeric", function(value, element) {
  return this.optional(element) || /^\w+$/i.test(value);
}, "Letteren en getallen allinich"); 


jQuery.validator.addMethod("lettersWithSpace", function(value, element) {
  return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "Brieven allinich");   
/*EXTRA VALIDATIONSF FOR JQUEY*/