<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */
defined('_JEXEC') or die;
JHtml::stylesheet('install.css', 'administrator/components/com_kunena/media/css/');
if (!$this->error && $this->step>0 && $this->step<count($this->steps)-1) {
	$document =& JFactory::getDocument();
	$document->addScriptDeclaration("window.addEvent('domready', function() {window.location='".JRoute::_('index.php?option=com_kunena&view=install&task=install', false)."';});");
}
?>
	<div id="stepbar">
		<div class="u">
			<div class="u">
				<div class="u"></div>
			</div>
		</div>
	<div class="n">
			<h1>Steps</h1>
			<?php $this->showSteps(); ?>
  	</div>
	<div class="c">
		<div class="c">
			<div class="c"></div>
		</div>
	</div>
  </div>

<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="u">
				<div class="u">
					<div class="u"></div>
				</div>
			</div>
			<div class="n">

				<div class="far-right">

							<div class="button1-left"><div class="next"><a onclick="<?php echo $this->getActionURL(); ?>"><?php echo $this->getAction(); ?></a></div></div>

				</div>
				<span class="step"><?php echo $this->steps[$this->step]['menu']; ?></span>

			</div>
			<div class="c">
				<div class="c">
					<div class="c"></div>
				</div>
			</div>
		</div>

		<div id="installer">
			<div class="u">
				<div class="u">
					<div class="u"></div>
				</div>
			</div>
			<div class="n">
<?php
if (!empty($this->requirements->fail)):
	echo $this->loadTemplate('reqfail');
elseif (!$this->step):
	echo $this->loadTemplate('start');
else:
	echo $this->loadTemplate('install');
endif;
?>
			</div>
      <div class="c">
        <div class="c">
          <div class="c"></div>
        </div>
      </div>
		</div>
	</div>
</div>

<div class="clr"></div>
