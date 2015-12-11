var hbs = function(){/*
 <div id="code">
  <fieldset>
   <legend>Complaint Type</legend>
   {{#Codes}}
   <input type="radio" id="{{Class}}-radio" name="code[]" data-class="code" value="{{Class}}" title="{{Hint}}" /><label for="{{Class}}-radio">{{{Name}}}</label><br />
   {{/Codes}}
  </fieldset>
 </div>
 {{#Codes}}
 <div id="{{Class}}" class="code hidden">
 {{#if Options}}
 <fieldset id="{{Class}}-options">
  <legend>Part</legend>
  {{#Options}}
  <input type="radio" id="{{../Class}}-{{Part}}-radio" data-class="{{../Class}}-options" name="{{../Class}}[]" value="{{../Class}}-{{Part}}" title="{{../Name}} Part {{Part}}" /><label for="{{../Class}}-{{Part}}-radio">{{{Title}}}</label><br />
  {{/Options}}
 </fieldset>
 {{/if}}
 {{#if Principles}}
 <fieldset id="{{Class}}-p">
  <legend>Principles</legend>
  {{#Principles}}
  <input type="checkbox" id="{{../Class}}-p-{{Clause}}-checkbox" name="{{../Class}}-p[]" value="{{../Class}}-p-{{Clause}}" title="{{../Name}} Principle {{Clause}}" /><label for="{{../Class}}-p-{{Clause}}-checkbox">{{{Infringement}}}</label> <span title="{{{Code}}}">?</span><br />
  <div id="{{../Class}}-p-{{Clause}}" class="claim">
   <div id="{{../Class}}-p-{{Clause}}-1">
    <input type="text" placeholder="URL" id="{{../Class}}-p-{{Clause}}-url-1" /><br />
    <textarea placeholder="Claims" id="{{../Class}}-p-{{Clause}}-claims-1" />
   </div>
  </div>
   {{#if Guidelines}}
   <div class="sub">
   {{#Guidelines}}
   <input type="checkbox" id="{{../../../Class}}-p-{{../Clause}}-{{Clause}}-checkbox" name="{{../../../Class}}-p[]" value="{{../../../Class}}-p-{{../Clause}}-{{Clause}}" title="{{../../../Name}} Principle {{../Clause}}, Guideline {{Clause}}" /><label for="{{../../../Class}}-p-{{../Clause}}-{{Clause}}-checkbox">{{{Infringement}}}</label> <span title="{{{Code}}}">?</span><br />
   <div id="{{../../../Class}}-p-{{../Clause}}-{{Clause}}" class="claim">
    <div id="{{../../../Class}}-p-{{../Clause}}-{{Clause}}-1">
     <input type="text" placeholder="URL" id="{{../../../Class}}-p-{{../Clause}}-{{Clause}}-url-1" /><br />
     <textarea placeholder="Claims" id="{{../../../Class}}-p-{{../Clause}}-{{Clause}}-claims-1" />
    </div>
   </div>
   {{/Guidelines}}
  </div>
  {{/if}}
  {{/Principles}}
 </fieldset>
 {{/if}}
 {{#Options}}
 {{#if Requirements}} 
 <div id="{{../../Class}}-{{../Part}}" class="{{../../Class}}-options hidden">
 <fieldset>
  <legend>Requirements</legend>
  {{#Requirements}}
  <input type="checkbox" id="{{../../../Class}}-{{../Part}}-r-{{Clause}}-checkbox" name="{{../../../Class}}-{{../Part}}[]" value="{{../../../Class}}-{{../Part}}-r-{{Clause}}" title="{{../../../Name}} Part {{../Part}}, Requirement {{Clause}}" /><label for="{{../../../Class}}-{{../Part}}-r-{{Clause}}-checkbox">{{{Infringement}}}</label> <span title="{{{Code}}}">?</span><br />
  <div id="{{../../../Class}}-{{../Part}}-r-{{Clause}}" class="claim">
   <div id="{{../../../Class}}-{{../Part}}-r-{{Clause}}-1">
    <input type="text" placeholder="URL" id="{{../../../Class}}-{{../Part}}-r-{{Clause}}-url-1" /><br />
    <textarea placeholder="Claims" id="{{../../../Class}}-{{../Part}}-r-{{Clause}}-claims-1" />
   </div>
  </div>
   {{#if Guidelines}}
   <div class="sub">
   {{#Guidelines}}
   <input type="checkbox" id="{{../../../../../Class}}-{{../../../Part}}-r-{{../Clause}}-{{Clause}}-checkbox" name="{{../../../../../Class}}-{{../../../Part}}[]" value="{{../../../../../Class}}-{{../../../Part}}-r-{{../Clause}}-{{Clause}}" title="{{../../../../../Name}} Part {{../../../Part}}, Requirement {{../Clause}}.{{Clause}}" /><label for="{{../../../../../Class}}-{{../../../Part}}-r-{{../Clause}}-{{Clause}}-checkbox">{{{Infringement}}}</label> <span title="{{{Code}}}">?</span><br />
   <div id="{{../../../../../Class}}-{{../../../Part}}-r-{{../Clause}}-{{Clause}}" class="claim">
    <div id="{{../../../../../Class}}-{{../../../Part}}-r-{{../Clause}}-{{Clause}}-1">
     <input type="text" placeholder="URL" id="{{../../../../../Class}}-{{../../../Part}}-r-{{../Clause}}-{{Clause}}-url-1" /><br />
     <textarea placeholder="Claims" id="{{../../../../../Class}}-{{../../../Part}}-r-{{../Clause}}-{{Clause}}-claims-1" />
    </div>
   </div>
  {{/Guidelines}}
  </div>
  {{/if}}
  {{/Requirements}}
  </fieldset>
  </div>
 {{/if}}
 {{/Options}}
 </div>
 {{/Codes}}
*/}.toString().slice(14,-3)