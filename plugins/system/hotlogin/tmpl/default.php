<?php

/**
 * @subpackage  system - HOT Login
 * @version     2.5.2
 * @author      EmmeAlfa
 * @license     GNU/GPL v.2
 * @see         /plugins/system/hotlogin/LICENSE.php
 */

 defined( '_JEXEC' ) or die( 'Restricted access' );
 $user = & JFactory::getUser();
 $status=$user->get('guest');
 $quicklogout=$this->params->get( 'quicklogout', 'n' );
 $fixed=$this->params->get( 'fixed');
 $position="absolute";
 if ($fixed=="y") { $position="fixed";}
?>

<script>
var hoffset= <?php echo $this->params->get( 'v_offset', '0' ); ?>;
<?php if ( ($status==0) and ($quicklogout=='y') ) : ?>   
function sendform() {
    var i;
    var mydiv=document.getElementById('HLrender');
    var elms =  mydiv.getElementsByTagName("*");
    for(var i = 0, maxI = elms.length; i < maxI; ++i) { 
        var elm = elms[i];
        if (elm.tagName=="FORM" ) {
           elm.submit();
           break;
        }
    }
} 
<?php endif; ?>  
</script>
<?php if ( !empty( $LoginModule ) ): ?>
    <div id="HLwrapper" style="position: <?php echo $position ?>; margin-top: -400px;">
       <div id="HLsiteemu" style="width: <?php echo $this->params->get( 'site_width', '900px' ); ?>;" >
        <div id="HLcontainer"  style="margin-right: <?php echo $this->params->get( 'tab_offset', '20' ); ?>px;">
            <div ID="HLhidden">
                 <div ID="HLmodule">
                      <div ID="HLrender">              
                        <?php echo JModuleHelper::renderModule( $LoginModule ); ?>
                      </div>
                 </div>
                 <div ID="HLsep">
                 </div>
            </div>
            <div id="HLhandle">
            <?php if  ($status==0)  : ?>
				<?php if ($quicklogout=='y')  : ?>
					<A href="#" id="HLtrigger" style="display:none;">.</a>
					<SPAN onClick="sendform();" style="cursor:pointer; <?php echo $this->params->get( 'handle_css', '' ); ?>"> <?php echo $this->params->get( 'tab_text_logged', 'Logout' ); ?></span> 
				<?php else : ?>
					<SPAN id="HLtrigger" style="cursor:pointer; <?php echo $this->params->get( 'handle_css', '' ); ?>"> <?php echo $this->params->get( 'tab_text_logged', 'Logout' ); ?></span> 
				<?php endif; ?>   					
            <?php else : ?>
                <SPAN id="HLtrigger" style="cursor:pointer; <?php echo $this->params->get( 'handle_css', '' ); ?>"> <?php echo $this->params->get( 'tab_text_not_logged', 'Login' ); ?></span> 
            <?php endif; ?>                   
            </div>
        </div>        
       </div> 
	</div>	
<?php endif; ?>    