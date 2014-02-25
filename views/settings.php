<?php defined('APPLICATION') or exit();?>

<h1><?php echo $this->Data('Title');?></h1>
<div class="Info"><?php echo T('Add WordPress Info Settings', 'Add WordPress Info shows additional input fields in new discussions.');?></div>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();

echo '<hr />';
echo $this->Form->Label('Please mark the categories to <strong>exclude</strong> from showing those fields.', 'Plugins.AddWPInfo.CategoryIDs');
echo $this->Form->CheckBoxList('Plugins.AddWPInfo.CategoryIDs', $this->CategoryData, $this->ExcludeCategory, array('ValueField' => 'CategoryID', 'TextField' => 'Name'));

echo '<hr />';
echo $this->Form->CheckBox('Plugins.AddWPInfo.KeepTags', T('Allow additional tags'));

echo '<hr />';
echo $this->Form->Button('Save');
echo $this->Form->Close();
