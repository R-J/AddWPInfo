<?php if (!defined('APPLICATION')) exit();

$PluginInfo['AddWPInfo'] = array(
   'Name' => 'Add WordPress Info',
   'Description' => 'Force users to add additional information to discussions',
   'Version' => '0.4',
   'RequiredApplications' => array('Vanilla' => '>=2.1b2'),
   'RequiredPlugins' => array('Tagging' => '1.6.2'),
   'SettingsUrl' => '/settings/addwpinfo',
   'SettingsPermission' => 'Garden.Settings.Manage',
   'RegisterPermissions' => array('Plugins.AddWPInfo.Manage'),
   'HasLocale' => FALSE,
   'Author' => 'Robin Jurinka',
   'License' => 'MIT',
   'MobileFriendly' => TRUE
);

class AddWPInfoPlugin extends Gdn_Plugin {
   protected $_WPVersions;
   protected $_WPVersionsDropDown;
   
   public function __construct() {
      $this->_WPVersions = array('WP 3.8.1', 'WP 3.8', 'WP 3.7.1', 'WP 3.7', 'WP 3.6.1', 'WP 3.6', 'WP 3.5.2', 'WP 3.5.1', 'WP 3.5', 'WP 3.4.2', 'WP 3.4.1', 'WP 3.4', 'WP 3.3.3', 'WP 3.3.2', 'WP 3.3.1', 'WP 3.3', 'WP 3.2.1', 'WP 3.2', 'WP 3.1.4', 'WP 3.1.3', 'WP 3.1.2', 'WP 3.1.1', 'WP 3.1', 'WP 3.0.6', 'WP 3.0.5', 'WP 3.0.4', 'WP 3.0.3', 'WP 3.0.2', 'WP 3.0.1', 'WP 3.0', 'WP 2.9.2', 'WP 2.9.1', 'WP 2.9', 'WP 2.8.6', 'WP 2.8.5', 'WP 2.8.4', 'WP 2.8.3', 'WP 2.8.2', 'WP 2.8.1', 'WP 2.8', 'WP 2.7.1', 'WP 2.7', 'WP 2.6.5', 'WP 2.6.3', 'WP 2.6.2', 'WP 2.6.1', 'WP 2.6', 'WP 2.5.1', 'WP 2.5', 'WP 2.3.3', 'WP 2.3.2', 'WP 2.3.1', 'WP 2.3', 'WP 2.2.3', 'WP 2.2.2', 'WP 2.2.1', 'WP 2.2', 'WP 2.1.3', 'WP 2.1.2', 'WP 2.1.1', 'WP 2.1', 'WP 2.0.11', 'WP 2.0.10', 'WP 2.0.9', 'WP 2.0.8', 'WP 2.0.7', 'WP 2.0.6', 'WP 2.0.5', 'WP 2.0.4', 'WP 2.0.3', 'WP 2.0.2', 'WP 2.0.1', 'WP 2.0', 'WP 1.5.2', 'WP 1.5.1.3', 'WP 1.5.1.2', 'WP 1.5.1', 'WP 1.5', 'WP 1.2.2', 'WP 1.2.1', 'WP 1.2', 'WP 1.0.2', 'WP 1.0.1', 'WP 1.0', 'WP 0.72', 'WP 0.711', 'WP 0.71', 'WP 0.70');
      $this->_WPVersionsDropDown = array_combine($this->_WPVersions, $this->_WPVersions);
   }

   /**
    * Extend database and init settings
    */
   public function Setup() {
      $Structure =  Gdn::Structure();
      $Structure->Table('Discussion')
         ->Column('WPInfoWPVersion', 'varchar(255)', TRUE)
         ->Column('WPInfoThemeVersion', 'varchar(255)', TRUE)
         ->Column('WPInfoThemeName', 'varchar(255)', TRUE)
         ->Set(FALSE, FALSE);
      if (!C('Plugins.AddWPInfo.Themes')) {
         SaveToConfig('Plugins.AddWPInfo.Themes', array('Thesis', 'Genesis'));
      }
      if (C('Plugins.AddWPInfo.KeepTags') == '') {
         SaveToConfig('Plugins.AddWPInfo.KeepTags', FALSE);
      }
   }

   /**
    *  Adds input fields to new discussion form
    */ 
   public function PostController_BeforeBodyInput_Handler($Sender) {
      $Sender->AddCssFile('addwpinfo.css', 'plugins/AddWPInfo');
      $Sender->AddJsFile('addwpinfo.js', 'plugins/AddWPInfo');
      $Sender->AddDefinition('AddWPInfoCategoryIDs', json_encode(C('Plugins.AddWPInfo.CategoryIDs')));

      $WPThemes = C('Plugins.AddWPInfo.Themes', array('Thesis', 'Genesis'));
      $WPThemes = array_combine($WPThemes, $WPThemes);
      
      $Url = $Sender->Form->Action;
      if ($Url != '/post/discussion') {
         $CategoryUrlCode = end(explode('/', $Url));
         $CategoryModel = new CategoryModel();
         $Category = $CategoryModel->GetFullByUrlCode($CategoryUrlCode);
         $CategoryID = $Category->CategoryID;
         if (in_array($CategoryID, C('Plugins.AddWPInfo.CategoryIDs'))) {
            $Hidden = ' Hidden';
         }
      }
      
      $HtmlOut = <<< EOT
<div class="P">
   <ul id="AddWPInfo" class="Tabs{$Hidden}">
      <li id="WPVersion">
         {$Sender->Form->Label(T('WordPress Version'), 'WPInfoWPVersion')}
         {$Sender->Form->DropDown(
            'WPInfoWPVersion',
            array_merge(array('0' => T('Please Choose')), $this->_WPVersionsDropDown)
         )}
      </li>
      <li id="ThemeVersion">
         {$Sender->Form->Label(T('Theme Version'), 'WPInfoThemeVersion')}
         {$Sender->Form->TextBox('WPInfoThemeVersion')}
      </li>
      <li id="ThemeName">
         {$Sender->Form->Label(T('Theme Name'), 'WPInfoThemeName')}
         {$Sender->Form->DropDown(
            'WPInfoThemeName',
            array_merge(array('0' => T('Please Choose')), $WPThemes)
         )}
      </li>
   </ul>
</div>
EOT;
      echo $HtmlOut;
   } // End of PostController_BeforeBodyInput_Handler
   
   /**
    *  Add Validation for custom fields
    *  Save custom fields as tags
    */
   public function DiscussionModel_BeforeSaveDiscussion_Handler($Sender) {
      $Session = Gdn::Session();
      $CategoryID = $Sender->EventArguments['FormPostValues']['CategoryID'];

      // exit if current category is excluded
      if (in_array($CategoryID, C('Plugins.AddWPInfo.CategoryIDs'))) {
         return;
      }

      // Add Validations for all roles without Plugins.AddWPInfo.Manage
      if(!$Session->CheckPermission('Plugins.AddWPInfo.Manage')) {    
         $Sender->Validation->ApplyRule('WPInfoWPVersion', 'Required', T('Please specify WordPress version number.'));
         $Sender->Validation->SetSchemaProperty('WPInfoWPVersion', 'Enum', $this->_WPVersions);
         $Sender->Validation->ApplyRule('WPInfoWPVersion', 'Enum', T('Choose one of the WordPress versions below.'));

         $Sender->Validation->ApplyRule('WPInfoThemeVersion', 'Required', T('Theme version number is required.'));
         $Sender->Validation->AddRule('RegexThemeVersion', 'regex:/^(\d{1,3}((\.\d{1,3})){0,3})$/');
         $Sender->Validation->ApplyRule('WPInfoThemeVersion', 'RegexThemeVersion', T('Theme version must be "X.Y.Z".'));
         
         $Sender->Validation->ApplyRule('WPInfoThemeName', 'Required', T('Please specify theme name.'));
         $Sender->Validation->SetSchemaProperty('WPInfoThemeName', 'Enum', C('Plugins.AddWPInfo.Themes', array('Thesis', 'Genesis')));
         $Sender->Validation->ApplyRule('WPInfoThemeName', 'Enum', T('Please choose a theme name.'));
      } 
      
      // Save Tags
      $Category = CategoryModel::Categories($CategoryID);
      $WPInfoTags = $Sender->EventArguments['FormPostValues']['WPInfoWPVersion'];
      if (strlen($Sender->EventArguments['FormPostValues']['WPInfoThemeVersion']) != 0) {
         $WPInfoTags .= ','.$Category['Name'].' '.$Sender->EventArguments['FormPostValues']['WPInfoThemeVersion'];
      }
      $WPInfoTags .= ','.$Sender->EventArguments['FormPostValues']['WPInfoThemeName'];
      $Tags = $Sender->EventArguments['FormPostValues']['Tags'];
      if (strlen($Tags) != 0 && C('Plugins.AddWPInfo.KeepTags') == TRUE) {
         // append if other tags are set
         $Sender->EventArguments['FormPostValues']['Tags'] = $Tags.','.$WPInfoTags;
      } else {
         $Sender->EventArguments['FormPostValues']['Tags'] = $WPInfoTags;
      }
   } // End of DiscussionModel_BeforeSaveDiscussion_Handler
   
   /**
    *  Dispatcher for settings screen
    */
   public function SettingsController_AddWPInfo_Create($Sender, $Args) {
      $Sender->Permission('Garden.Settings.Manage');
      return $this->Dispatch($Sender);
   } // End of SettingsController_AddWPInfo_Create
   
   /**
    * Define categories to exclude
    */
   public function Controller_Index($Sender) {
      $Sender->Title(T('Add WordPress Info Settings'));
      $Sender->AddSideMenu('settings/addwpinfo');

      $Validation = new Gdn_Validation();
      $Validation->ApplyRule('Plugins.AddWPInfo.CategoryIDs', 'RequiredArray');
      $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
      $ConfigurationModel->SetField(array('Plugins.AddWPInfo.CategoryIDs', 'Plugins.AddWPInfo.KeepTags'));

      $Form = $Sender->Form;
      $Sender->Form->SetModel($ConfigurationModel);

      if ($Sender->Form->AuthenticatedPostBack() != FALSE) {
         if ($Sender->Form->Save() != FALSE) {
            $Sender->StatusMessage = T('Saved');
         }
      } else {
         $Sender->Form->SetData($ConfigurationModel->Data);
      }

      $CategoryModel = new Gdn_Model('Category');
      $Sender->CategoryData = $CategoryModel->GetWhere(array('AllowDiscussions' => 1, 'CategoryID <>' => -1));
      $Sender->ExcludeCategory = C('Plugins.AddWPInfo.CategoryIDs');

      $Sender->Render('settings', '', 'plugins/AddWPInfo');
   } // End of SettingsController_WPInfo_Create
}
