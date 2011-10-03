<div class="jqrte_body">
<table>
<tr>
   <td>
   <table class="jqrte_menu">
      <tr>
         <td colspan="4" title="<?php echo $label[$language]["Select Format"];?>">
            <select name="formatblock" id="content_rte_formatblock" style="width:105px" >
               <option value="" selected="selected"><?php echo $label[$language]["Select Format"];?></option>
               <option value="&lt;p&gt;">Paragraph</option>
               <option value="&lt;pre&gt;">Pre</option>
               <option value="&lt;h6&gt;">Heading 6</option>
               <option value="&lt;h5&gt;">Heading 5</option>
               <option value="&lt;h4&gt;">Heading 4</option>
               <option value="&lt;h3&gt;">Heading 3</option>
               <option value="&lt;h2&gt;">Heading 2</option>
               <option value="&lt;h1&gt;">Heading 1</option>
            </select>
         </td>
         <td colspan="4" title="<?php echo $label[$language]["Font Fmaily"];?>">	
            <select name="fontname" id="content_rte_fontname" style="width:96px">
               <option value="" selected="selected"><?php echo $label[$language]["Select Font"];?></option>
               <option value="arial"><?php echo $label[$language]["Arial"];?></option>
               <option value="comic sans ms"><?php echo $label[$language]["Comic Sans"];?></option>
               <option value="courier new"><?php echo $label[$language]["Courier New"];?></option>
               <option value="georgia"><?php echo $label[$language]["Georgia"];?></option>
               <option value="helvetica"><?php echo $label[$language]["Helvetica"];?></option>
               <option value="impact"><?php echo $label[$language]["Impact"];?></option>
               <option value="times new roman"><?php echo $label[$language]["Times"];?></option>
               <option value="trebuchet ms"><?php echo $label[$language]["Trebuchet"];?></option>
               <option value="verdana"><?php echo $label[$language]["Verdana"];?></option>
            </select>
         </td>
         <td colspan="4" title="<?php echo $label[$language]["Select Font Size"];?>">
            <select name="fontsize" id="content_rte_fontsize" style="width:119px">
               <option value="" selected="selected"><?php echo $label[$language]["Select Font Size"];?></option>
               <option value="1">8</option>
               <option value="2">10</option>
               <option value="3">12</option>
               <option value="4">14</option>
               <option value="5">18</option>
               <option value="6">24</option>
            </select>
         </td>
		 <td id="content_rte_bgcolor" title="<?php echo $label[$language]["Background Color"];?>"></td>
         <td id="content_rte_forecolor" title="<?php echo $label[$language]["Font Color"];?>"></td>
         <td id="content_rte_bold" title="<?php echo $label[$language]["Bold"];?>"></td>
         <td id="content_rte_italic" title="<?php echo $label[$language]["Italic"];?>"></td>
         <td id="content_rte_underline" title="<?php echo $label[$language]["Underline"];?>"></td>
         <td id="content_rte_strikethrough" title="<?php echo $label[$language]["Strikethrough"];?>"></td>
		 <td id="content_rte_justifyleft" title="<?php echo $label[$language]["Justify Left"];?>"></td>
		 <td id="content_rte_copyright" title="<?php echo $label[$language]["Copyright"];?>"></td>
      </tr>
      <tr>        
         <td id="content_rte_justifycenter" title="<?php echo $label[$language]["Justify Center"];?>"></td>
         <td id="content_rte_justifyright" title="<?php echo $label[$language]["Justify Right"];?>"></td>
         <td id="content_rte_justifyfull" title="<?php echo $label[$language]["Justify Full"];?>"></td>
         <td id="content_rte_insertorderedlist" title="<?php echo $label[$language]["Insert Ordered List"];?>"></td>
         <td id="content_rte_insertunorderedlist" title="<?php echo $label[$language]["Insert Unordered List"];?>"></td>
         <td id="content_rte_insertHorizontalRule" title="<?php echo $label[$language]["Insert Horizontal Rule"];?>"></td>
         <td id="content_rte_removeformat" title="<?php echo $label[$language]["Remove Format"];?>"></td>
         <td id="content_rte_addlink" title="<?php echo $label[$language]["Add Link"];?>"></td>
         <td id="content_rte_unlink" title="<?php echo $label[$language]["Unlink"];?>"></td>
         <td id="content_rte_addtable" title="<?php echo $label[$language]["Add Table"];?>"></td>
         <td id="content_rte_addimage" title="<?php echo $label[$language]["Add Image"];?>"></td>
         <td id="content_rte_character" title="<?php echo $label[$language]["Special Character"];?>"></td>
         <td id="content_rte_emotion" title="<?php echo $label[$language]["Emotion"];?>"></td>
         <td id="content_rte_indent" title="<?php echo $label[$language]["Indent"];?>"></td>
         <td id="content_rte_outdent" title="<?php echo $label[$language]["Outdent"];?>"></td>
		 <td id="content_rte_subscript" title="<?php echo $label[$language]["Subscript"];?>"></td>
         <td id="content_rte_superscript" title="<?php echo $label[$language]["Superscript"];?>"></td>
         <td id="content_rte_html" title="<?php echo $label[$language]["Html Content"];?>"></td>

      </tr>
   </table>
   </td>
</tr>
<tr>
   <td>
      <iframe id="content_rte" src="about:blank" class="jqrte_iframebody"></iframe>
   </td>
</tr>
</table>
</div>