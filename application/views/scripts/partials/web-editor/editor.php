<div id="color_div" style="display:none" title="<?php echo $label[$language]["Color Picker"];?>">
   <table>
      <tr>
         <td colspan="3">
            H:<input type="text" id="jqcp_h" size="3" value="0">
            S:<input type="text" id="jqcp_s" size="3" value="0">
            L:<input type="text" id="jqcp_l" size="3" value="0"><br>
            R:<input type="text" id="jqcp_r" size="3" value="255">
            G:<input type="text" id="jqcp_g" size="3" value="255">
            B:<input type="text" id="jqcp_b" size="3" value="255"><br>
            <input type="text" id="color_value" class="jqcp_value" size="8">
            <input type="button" id="color_btn" value="<?php echo $label[$language]["Pick"];?>">
         </td>
      </tr>
      <tr>
         <td align="left"><div id="color_picker"></div></td>
      </tr>
   </table>
</div>


<div id="character_div" style="display:none" title="<?php echo $label[$language]["Special Character"];?>">
</div>


<div id="addtable_div" style="display:none" title="<?php echo $label[$language]["Add Table"];?>">
   <table>
      <tr>
         <td><?php echo $label[$language]["Rows"];?></td>
         <td><input type="text" id="addtable_row" name="table_row" value="2" size="10"></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Columns"];?></td>
         <td><input type="text" id="addtable_column" name="table_column" value="2" size="10"></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Width"];?></td>
         <td><input type="text" id="addtable_width" name="table_width" value="100" size="10"></td>
         <td>
            <select name="table_width_format" id="addtable_format">
               <option value="%">%</option>
               <option value=""><?php echo $label[$language]["pixels"];?></option>
            </select>
         </td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Border"];?></td>
         <td><input type="text" id="addtable_border" name="table_border" value="1" size="10"></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Cellspacing"];?></td>
         <td><input type="text" id="addtable_cellspacing" name="table_cellspacing" value="0" size="10"></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Cellpadding"];?></td>
         <td><input type="text" id="addtable_cellpadding" name="table_cellpadding" value="0" size="10"></td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Alignment"];?></td>
         <td>
            <select id="addtable_alignment" name="table_alignment">
               <option value=""><?php echo $label[$language]["default"];?></option>
               <option value="left"><?php echo $label[$language]["left"];?></option>
               <option value="right"><?php echo $label[$language]["right"];?></option>
               <option value="center"><?php echo $label[$language]["center"];?></option>
            </select>
         </td>
         <td>&nbsp;</td>
      </tr>
      <tr>
         <td>
            <input type="button" id="addtable_btn" value="<?php echo $label[$language]["Submit"];?>">
         </td>
      </tr>
   </table>
</div>


<div id="addlink_div" style="display:none"  title="<?php echo $label[$language]["Add Link"];?>">
   <table>
      <tr>
         <td><?php echo $label[$language]["Site Name"];?></td>
         <td><input type="text" id="addlink_name" name="link_name" size="20"></td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["URL"];?></td>
         <td><input type="text" id="addlink_url" name="link_url"></td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Target"];?></td>
         <td>
            <select name="link_target" id="addlink_target">
               <option value=""></option>
               <option value="_blank"><?php echo $label[$language]["_blank"];?></option>
               <option value="_parent"><?php echo $label[$language]["_parent"];?></option>
               <option value="_self"><?php echo $label[$language]["_self"];?></option>
               <option value="_top"><?php echo $label[$language]["_top"];?></option>
            </select>
         </td>
      </tr>
      <tr>
         <td>
            <input type="button" id="addlink_btn" value="<?php echo $label[$language]["Submit"];?>">
         </td>
      </tr>
   </table>
</div>

<div id="addimage_div" style="display:none" title="<?php echo $label[$language]["Add Image"];?>">
   <table>
      <tr>
         <td><?php echo $label[$language]["Image URL"];?></td>
         <td><input type="text" id="addimage_url" name="image_url"></td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Image Description"];?></td>
         <td><input type="text" id="addimage_desc" name="image_desc"></td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Alignment"];?></td>
         <td>
            <select name="image_alignment" id="addimage_alignment">
               <option value=""></option>
               <option value="left"><?php echo $label[$language]["left"];?></option>
               <option value="right"><?php echo $label[$language]["right"];?></option>
            </select>
         </td>
      </tr>
      <tr>
         <td><?php echo $label[$language]["Border"];?></td>
         <td><input type="text" id="addimage_border" name="image_border" value="0" size="10"></td>
      </tr>
      <tr>
         <td>
            <input type="button" id="addimage_btn" value="<?php echo $label[$language]["Submit"];?>">
         </td>
      </tr>
   </table>
</div>


<div id="emotion_div" style="display:none"  title="<?php echo $label[$language]["Emotion"];?>">
   <table>
      <tr>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/angry.gif'); ?>"  alt="<?php echo $label[$language]["angry"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/cry.gif'); ?>" alt="<?php echo $label[$language]["cry"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/die.gif'); ?>" alt="<?php echo $label[$language]["die"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/dislike.gif'); ?>" alt="<?php echo $label[$language]["dislike"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/embarass.gif'); ?>" alt="<?php echo $label[$language]["embarass"];?>"></td>
      </tr>
      <tr>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/laugh.gif'); ?>" alt="<?php echo $label[$language]["laugh"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/nochoice.gif'); ?>" alt="<?php echo $label[$language]["nochoice"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/sad.gif'); ?>" alt="<?php echo $label[$language]["sad"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/smile.gif'); ?>" alt="<?php echo $label[$language]["smile"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/suprise.gif'); ?>" alt="<?php echo $label[$language]["suprise"];?>"></td>
      </tr>
      <tr>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/tongue.gif'); ?>" alt="<?php echo $label[$language]["tongue"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/wink.gif'); ?>" alt="<?php echo $label[$language]["wink"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/worry.gif'); ?>" alt="<?php echo $label[$language]["worry"];?>"></td>
         <td><img class="emoticon" src="<?php echo $this->baseUrl('images/emotions/yell.gif'); ?>" alt="<?php echo $label[$language]["yell"];?>"></td>
         <td></td>
      </tr>
   </table>
</div>
<div id="html_div" style="display:none" title="<?php echo $label[$language]["Html Content"];?>">
   <textarea id="html_content" rows="8" cols="50"></textarea><br>
   <input type="button" id="html_btn" value="submit">
</div>