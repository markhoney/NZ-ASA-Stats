$(function() {
 var ASACodes = $.getJSON("ASACodes.json");
 $("#asa").replaceWith('<fieldset id="code"><legend>Complaint type</legend></fieldset>');
 $.each( ASACodes, function(codeKey, Code) {
  $("#code").append('<input class="radio" type="radio" name="code" value="' + codeKey + '" title="' + Code.Hint + '">' + Code.Title + '</input>');
  $("#asa").append('<div id="' + codeKey + '"><h2><a href="' + Code.URL + '" target="_blank">' + Code.Title + '</a></h2><fieldset id="' + codeKey + 'p" class="hidden"><legend>Principles</legend></fieldset></div>');
  $.each(Code.Principles, function(principleNumber, Principle) {
   $("#" + codeKey + "p").append('<input type="checkbox" name="' +  + '" value="Principle ' + principleNumber + '" title="' + Principle.Code + '">' + Principle.ShouldNot + '</input>');
   if (typeof(Principle.Guidelines) != "undefined")
   {
    $.each(Principle.Guidelines, function(guidelineLetter, Guideline) {
     $("#" + codeKey + "p").append('<input type="checkbox" name="' +  + '" value="Principle ' + principleNumber + ', Guideline ' + guidelineLetter + '" title="' + Guideline.Code + '">' + Guideline.ShouldNot + '</input>');
    });
   }
  });
  if (typeof(Code.Requirements) != "undefined")
  {
   $.each(Code.Requirements, function(sectionCode, Section) {
    $("#" + codeKey).append('<fieldset id="' + codeKey + 'r' + sectionCode + '"><legend>Requirements</legend></fieldset>');
    
    $.each(Section.Requirements, function(requirementNumber, Requirement) {
     $("#" + codeKey + "r" + sectionCode).append('<input type="checkbox" name="' +  + '" value="Part ' + sectionCode + ', Requirement ' + requirementNumber + '" title="' + Requirement.Code + '">' + Requirement.ShouldNot + '</input>');
     if (typeof(Requirement.SubRequirements) != "undefined")
     {
      $.each(Requirement.SubRequirements, function(subRequirementNumber, SubRequirement) {
       $("#" + codeKey + "r").append('<input type="checkbox" name="' +  + '" value="Part ' + sectionCode + ', Requirement ' + requirementNumber + '.' + subRequirementNumber + '" title="' + SubRequirement.Code + '">' + SubRequirement.ShouldNot + '</input>');
      });
     }
    });
   });
  }
 });
});