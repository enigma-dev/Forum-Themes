<?php
/*
  BBCode Parser/Code highlighter copyright 2009 Josh Ventura
  This thing's released under the GNU General Public license, version 3 or later.
  You are free to use, modify, and distribute this code under the terms of said license.
  This is intended to be useful, but I guarantee nothing. It has NO warranty.
  For the license, see <http://www.gnu.org/licenses/>
*/

function syntahighlight($code,$cpp,$gml)
{
  $comms = '<font color="#00A000">';
  $comme = "</font>";
  
  $sstrs = '<font color="#888888">';
  $sstre = "</font>";
  
  $istrs = '<font color="#FFA000">';
  $istre = "</font>";
  
  $numrs = '<font color="#FF00D0">';
  $numre = "</font>";
  
  $mcros = '<font color="#0090FF">';
  $mcroe = "</font>";
  
  $cppdecl = array(
    "bool"=>0,  "char"=>0,  "int"=>0,  "float"=>0,  "double"=>0,  "long"=>0,
    "short"=>0,  "signed"=>0,  "unsigned"=>0,  "const"=>0,  "static"=>0,
    "volatile"=>0,  "register"=>0,  "auto"=>0
  );
  $gmldecl = array(
    "global"=>0,  "local"=>0,  "var"=>0
  );
  $decls = '<font color="#000088"><b>';
  $decle = "</b></font>";
  
  $cppkeyw = array(
    "asm"=>0, "break"=>0, "case"=>0, "catch"=>0,  "class"=>0,  "const_cast"=>0,  "continue"=>0,  "default"=>0,  "delete"=>0,  
    "do"=>0,  "dynamic_cast"=>0,  "else"=>0,  "enum"=>0,  "explicit"=>0,  "export"=>0,  "extern"=>0,  "false"=>0,  "for"=>0,  
    "friend"=>0,  "goto"=>0,  "if"=>0,  "inline"=>0,  "mutable"=>0,  "namespace"=>0,  "new"=>0,  "operator"=>0,
    "private"=>0,  "protected"=>0,  "public"=>0,  "reinterpret_cast"=>0,  "return"=>0,  "sizeof"=>0,  "static_cast"=>0,  
    "struct"=>0,  "switch"=>0,  "template"=>0,  "this"=>0,  "throw"=>0,  "true"=>0,  "try"=>0,  "typedef"=>0,  "typeid"=>0,  
    "typename"=>0,  "union"=>0,  "unsigned"=>0,  "using"=>0,  "virtual"=>0,  "void"=>0,  "volatile"=>0,  "wchar_t"=>0,  "while"=>0,
    "and"=>0, "or"=>0, "xor"=>0, "not"=>0, "bitand"=>0, "bitor"=>0
  );
  $gmlkeyw = array(
    "all"=>0, "break"=>0, "begin"=>0, "case"=>0, "continue"=>0,  "default"=>0,
    "do"=>0,  "else"=>0,  "end"=>0,  "exit"=>0,  "false"=>0,  "for"=>0,  "if"=>0,  
    "noone"=>0,  "other"=>0,  "local"=>0,  "return"=>0,  "repeat"=>0,  
    "self"=>0, "switch"=>0, "then"=>0,  "true"=>0,  "while"=>0,  
    "other"=>0,  "with"=>0,  "and"=>0, "or"=>0, "xor"=>0
  );
  $keyws = '<b>';
  $keywe = "</b>";
  
  
  $gmlconsts = array(
    "bm_normal"=>0, "bm_add"=>0, "bm_max"=>0, "bm_subtract"=>0, "bm_zero"=>0, "bm_one"=>0, "bm_src_color"=>0, "bm_inv_src_color"=>0, 
    "bm_src_alpha"=>0, "bm_inv_src_alpha"=>0, "bm_dest_alpha"=>0, "bm_inv_dest_alpha"=>0, "bm_dest_color"=>0, "bm_inv_dest_color"=>0, 
    "pr_pointlist"=>0, "pr_linelist"=>0, "pr_linestrip"=>0, "pr_trianglelist"=>0, "pr_trianglestrip"=>0, "pr_trianglefan"=>0, 
    "pr_lineloop"=>0, "pr_quadlist"=>0, "pr_quadstrip"=>0, "pr_polygon"=>0, "c_aqua"=>0, "c_black"=>0, "c_blue"=>0, "c_dkgray"=>0, 
    "c_fuchsia"=>0, "c_gray"=>0, "c_green"=>0, "c_lime"=>0, "c_ltgray"=>0, "c_maroon"=>0, "c_navy"=>0, "c_olive"=>0, "c_purple"=>0, 
    "c_red"=>0, "c_silver"=>0, "c_teal"=>0, "c_white"=>0, "c_yellow"=>0, "cr_default"=>0, "cr_none"=>0, "cr_arrow"=>0, "cr_cross"=>0, 
    "cr_beam"=>0, "cr_size_nesw"=>0, "cr_size_ns"=>0, "cr_size_nwse"=>0, "cr_size_we"=>0, "cr_uparrow"=>0, "cr_hourglass"=>0, 
    "cr_drag"=>0, "cr_nodrop"=>0, "cr_hsplit"=>0, "cr_vsplit"=>0, "cr_multidrag"=>0, "cr_sqlwait"=>0, "cr_no"=>0, "cr_appstart"=>0, 
    "cr_help"=>0, "cr_handpoint"=>0, "cr_size_all"=>0, "all"=>0, "noone"=>0, "self"=>0, "other"=>0, "global"=>0, "local"=>0, "true"=>0, 
    "false"=>0, "pi"=>0, "mb_any"=>0, "mb_none"=>0, "mb_left"=>0, "mb_right"=>0, "mb_middle"=>0, "vk_left"=>0, "vk_right"=>0, "vk_up"=>0, 
    "vk_down"=>0, "vk_control"=>0, "vk_alt"=>0, "vk_shift"=>0, "vk_space"=>0, "vk_enter"=>0, "vk_numpad0"=>0, "vk_numpad1"=>0, "vk_numpad2"=>0, 
    "vk_numpad3"=>0, "vk_numpad4"=>0, "vk_numpad5"=>0, "vk_numpad6"=>0, "vk_numpad7"=>0, "vk_numpad8"=>0, "vk_numpad9"=>0, "vk_divide"=>0, 
    "vk_multiply"=>0, "vk_subtract"=>0, "vk_add"=>0, "vk_decimal"=>0, "vk_f1"=>0, "vk_f2"=>0, "vk_f3"=>0, "vk_f4"=>0, "vk_f5"=>0, "vk_f6"=>0, 
    "vk_f7"=>0, "vk_f8"=>0, "vk_f9"=>0, "vk_f10"=>0, "vk_f11"=>0, "vk_f12"=>0, "vk_backspace"=>0, "vk_escape"=>0, "vk_home"=>0, "vk_end"=>0, 
    "vk_pageup"=>0, "vk_pagedown"=>0, "vk_delete"=>0, "vk_insert"=>0
  );
  $conss = '<font color="#A00000">';
  $conse = "</font>";
  
  $gmlglobals = array (
    "argument"=>0, "argument0"=>0, "argument1"=>0, "argument10"=>0, "argument11"=>0, "argument12"=>0, 
    "argument13"=>0, "argument14"=>0, "argument15"=>0, "argument2"=>0, "argument3"=>0, "argument4"=>0, 
    "argument5"=>0, "argument6"=>0, "argument7"=>0, "argument8"=>0, "argument9"=>0, "argument_relative"=>0, 
    "background_alpha"=>0, "background_blend"=>0, "background_color"=>0, "background_foreground"=>0, "background_height"=>0, 
    "background_hspeed"=>0, "background_htiled"=>0, "background_index"=>0, "background_showcolor"=>0, 
    "background_visible"=>0, "background_vspeed"=>0, "background_vtiled"=>0, "background_width"=>0, "background_x"=>0, 
    "background_xscale"=>0, "background_y"=>0, "background_yscale"=>0, "caption_health"=>0, "caption_lives"=>0, 
    "caption_score"=>0, "current_day"=>0, "current_hour"=>0, "current_minute"=>0, "current_month"=>0, 
    "current_second"=>0, "current_time"=>0, "current_weekday"=>0, "current_year"=>0, "cursor_sprite"=>0, 
    "error_last"=>0, "error_occurred"=>0, "event_action"=>0, "event_number"=>0, "event_object"=>0, "event_type"=>0, 
    "fps"=>0, "game_id"=>0, "health"=>0, "instance_count"=>0, "instance_id"=>0, "keyboard_key"=>0, 
    "keyboard_lastchar"=>0, "keyboard_lastkey"=>0, "keyboard_string"=>0, "lives"=>0, "mouse_button"=>0, 
    "mouse_lastbutton"=>0, "mouse_x"=>0, "mouse_y"=>0, "room"=>0, "room_caption"=>0, "room_first"=>0, 
    "room_height"=>0, "room_last"=>0, "room_persistent"=>0, "room_speed"=>0, "room_width"=>0, "score"=>0, 
    "secure_mode"=>0, "show_health"=>0, "show_lives"=>0, "show_score"=>0, "temp_directory"=>0, "transition_kind"=>0, 
    "transition_steps"=>0, "transition_time"=>0, "view_angle"=>0, "view_current"=>0, "view_enabled"=>0, 
    "view_hborder"=>0, "view_hport"=>0, "view_hspeed"=>0, "view_hview"=>0, "view_object"=>0, "view_vborder"=>0, 
    "view_visible"=>0, "view_vspeed"=>0, "view_wport"=>0, "view_wview"=>0, "view_xport"=>0, "view_xview"=>0, 
    "view_yport"=>0, "view_yview"=>0, "working_directory"=>0
  );
  $globs = '<font color = "#0000FF">';
  $globe = "</font>";
  
  $gmllocals = array (
    "alarm"=>0, "bbox_bottom"=>0, "bbox_left"=>0, "bbox_right"=>0, "bbox_top"=>0, "depth"=>0, "direction"=>0, 
    "friction"=>0, "gravity"=>0, "gravity_direction"=>0, "hspeed"=>0, "id"=>0, "image_alpha"=>0, "image_angle"=>0, 
    "image_blend"=>0, "image_index"=>0, "image_number"=>0, "image_single"=>0, "image_speed"=>0, "image_xscale"=>0, 
    "image_yscale"=>0, "mask_index"=>0, "object_index"=>0, "path_endaction"=>0, "path_index"=>0, "path_orientation"=>0, 
    "path_position"=>0, "path_positionprevious"=>0, "path_scale"=>0, "path_speed"=>0, "persistent"=>0, "solid"=>0, 
    "speed"=>0, "sprite_height"=>0, "sprite_index"=>0, "sprite_width"=>0, "sprite_xoffset"=>0, "sprite_yoffset"=>0, 
    "timeline_index"=>0, "timeline_position"=>0, "timeline_speed"=>0, "visible"=>0, "vspeed"=>0, "x"=>0, 
    "xprevious"=>0, "xstart"=>0, "y"=>0, "yprevious"=>0, "ystart"=>0
  );
  $locls = '<font color = "#0000FF">';
  $locle = "</font>";
  
  $funcs = '<font color="#0000A0">';
  $funce = "</font>";
  
  
  
  $pos=0;
  $out = "";
  $lout = 0;
  $plout = 0;
  $pfunc = false;
  $funcbuf  =  "";
  $canmacro = false;
  $macrocomment = false;
  
  for ($pos=0; $code[$pos] != ""; $pos++)
  {
    if (ord($code[$pos]) == 10 or ord($code[$pos]) == 13)
    {
      $canmacro = 1;
      continue;
    }
    if (isWhite($code[$pos]))
      continue;
    
    if (isLetter($code[$pos]) or $code[$pos] == '_')
    {
      $pfunc = false;
      $canmacro = false;
      $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
      $lout = $pos;
      
      $sp = $pos; $pos++;
      while (isLetterD($code[$pos]) or $code[$pos] == '_')
      $pos++;
      
      $nw = substr($code,$sp,$pos - $sp);
      if ($cpp)
      {
        if (array_key_exists($nw,$cppdecl))
        {
          $out .= $decls . $nw . $decle; //Add the colored text
          $lout = $pos--;
          continue;
        }
        elseif (array_key_exists($nw,$cppkeyw))
        {
          $out .= $keyws . $nw . $keywe; //Add the colored text
          $lout = $pos--;
          continue;
        }
      }
      if ($gml)
      {
        if (array_key_exists($nw,$gmldecl))
        {
          $out .= $decls . $nw . $decle; //Add the colored text
          $lout = $pos--;
          continue;
        }
        elseif (array_key_exists($nw,$gmlkeyw))
        {
          $out .= $keyws . $nw . $keywe; //Add the colored text
          $lout = $pos--;
          continue;
        }
        elseif (array_key_exists($nw,$gmlconsts))
        {
          $out .= $conss . $nw . $conse; //Add the colored text
          $lout = $pos--;
          continue;
        }
        elseif (array_key_exists($nw,$gmlglobals))
        {
          $out .= $globs . $nw . $globe; //Add the colored text
          $lout = $pos--;
          continue;
        }
        elseif (array_key_exists($nw,$gmllocals))
        {
          $out .= $locls . $nw . $locle; //Add the colored text
          $lout = $pos--;
          continue;
        }
      }
      $pfunc = true;
      $funcbuf = "";
      $plout = $lout;
      $pos--; continue;
    }
    if ($code[$pos] == '(')
    {
      if ($pfunc)
      {
        //Flush what we've skipped, with tags
        if ($funcbuf == "")
          $out .= $funcs . substr($code, $lout, $pos - $lout) . $funce;
        else
          $out .= $funcs . $funcbuf . substr($code, $plout, $pos - $plout) . $funce;
        $lout = $pos;
      }
      $pfunc = false;
      $canmacro = false;
      $funcbuf = "";
      continue;
    }
    
    if ($code[$pos] == '/')
    {
      $sp = $pos; 
      if ($code[$pos + 1] == '/')
      {
        if ($pfunc)
          $funcbuf .= substr($code, $plout, $pos - $plout); //Flush what we've skipped
        else
          $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
        
        $pos++;
        while ($code[$pos] != "" and ord($code[$pos]) != 10 and ord($code[$pos]) != 13)
          $pos++;
        
        if ($pfunc)
        {
          $funcbuf .= $comms . substr($code,$sp,$pos - $sp) . $comme;
          $plout = $pos;
        }
        else
        {
          $out .= $comms . substr($code,$sp,$pos - $sp) . $comme;
          $lout = $pos;
        }
        
        $pos--; continue;
      }
      if ($code[$pos + 1] == '*')
      {
        if ($pfunc)
          $funcbuf .= substr($code, $plout, $pos - $plout); //Flush what we've skipped
        else
          $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
        
        $pos+=2;
        if ($code[$pos] != "" and ! $gml)
          $pos++;
          
        while ($code[$pos] != "" and ($code[$pos-1] != "*" or $code[$pos] != "/"))
          $pos++;
        
        $pos++;
        
        if ($pfunc)
        {
          $funcbuf .= $comms . substr($code,$sp,$pos - $sp) . $comme;
          $plout = $pos;
        }
        else
        {
          $out .= $comms . substr($code,$sp,$pos - $sp) . $comme;
          $lout = $pos;
        }
        
        if ($macrocomment)
        {
          $pos--; //back at the / in */
          $code[$pos] = '#';
        }
        $pos--; continue;
      }
      $pfunc = false;
      continue;
    }
    
    if ($cpp)
    {
      if ($code[$pos] == '#' and $canmacro)
      {
        if ($pfunc)
          $funcbuf .= substr($code, $plout, $pos - $plout); 
        else if (! $macrocomment)
          $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
        
        $sp = $pos; $pos++;
        if ($macrocomment)
        {
          $sp++;
          $macrocomment = false;
        }
        
        while ($code[$pos] != "" and ord($code[$pos]) != 10 and ord($code[$pos]) != 13)
        {
          if ($code[$pos] == "/" and $code[$pos+1] == "/") break;
          if ($code[$pos] == "/" and $code[$pos+1] == "*")
          {
            $macrocomment = true;
            break;
          }
          $pos++;
        }
        
        if ($pfunc)
        {
          $funcbuf .= $mcros . substr($code,$sp,$pos - $sp) . $mcroe;
          $plout = $pos;
        }
        else
        {
          $out .= $mcros . substr($code,$sp,$pos - $sp) . $mcroe;
          $lout = $pos;
        }
        $pos--; continue;
      }
    }
    
    //Nothing past here can occur between a function identifier and its () parameters
    $pfunc = false;
    
    
    
    if (isDigit($code[$pos]))
    {
      $pfunc = false;
      $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
      $sp = $pos;
      if ($gml)
      {
        while (isDigit($code[$pos]))
          $pos++;
      }
      else
      {
        while (isLetterD($code[$pos]))
          $pos++;
      }
      $lout = $pos;
      $out .= $numrs . substr($code,$sp,$pos - $sp) . $numre; //Add the colored text
      $pos--; continue;
    }
    
    if ($code[$pos] == '$' and $gml)
    {
      $pfunc = false;
      $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
      $sp = $pos++;
      
      while (isDigit($code[$pos]) or (ord($code[$pos]) >= 65 and ord($code[$pos]) <= 70) or (ord($code[$pos]) >= 97 and ord($code[$pos]) <= 102))
        $pos++;
      
      $lout = $pos;
      $out .= $numrs . substr($code,$sp,$pos - $sp) . $numre; //Add the colored text
      $pos--; continue;
    }
    
    if (substr($code,$pos,6) == '&quot;')
    {
      $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
      $sp = $pos; $pos++;
      while ($code[$pos] != "" and substr($code,$pos,6) != '&quot;')
      {
        if (! $gml)
        {
          if ($code[$pos] == '\\' and ($code[$pos + 1] == '\\' or $code[$pos + 1] == '&'))
            $pos++;
          if (ord($code[$pos]) == 10 or ord($code[$pos]) == 13)
          {
            $pos -= 6;
            break;
          }
        }
        $pos++;
      }
      $pos += 6;
      $out .= $sstrs . substr($code,$sp,$pos - $sp) . $sstre; //Add the colored text
      $lout = $pos;
      $pos--; continue;
    }
    
    if ($code[$pos] == "'")
    {
      $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
      $sp = $pos; $pos++;
      while ($code[$pos] != "" and $code[$pos] != "'")
      {
        if (! $gml)
        {
          if ($code[$pos] == '\\' and ($code[$pos + 1] == '\\' or $code[$pos] == "'"))
            $pos++;
          if (ord($code[$pos]) == 10 or ord($code[$pos]) == 13)
          {
            $pos--;
            break;
          }
        }
        $pos++;
      }
      $pos++;
      $out .= ((! $gml)?$istrs:$sstrs) . substr($code,$sp,$pos - $sp) . ((! $gml)?$istre:$sstre); //Add the colored text
      $lout = $pos;
      $pos--; continue;
    }
  }
  $out .= substr($code, $lout, $pos - $lout); //Flush what we've skipped
  if ($out == "") $out = "No code to parse.";

  return $out;
}
?>
