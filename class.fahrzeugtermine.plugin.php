<?php

class FahrzeugTerminePlugin extends Gdn_Plugin {

    public function Base_Render_Before($Sender) {
        $Session = Gdn::Session();

        if (($Sender->Menu) &&
                ((CheckPermission('Plugins.FahrzeugTermine.View')) ||
                (CheckPermission('Plugins.FahrzeugTermine.Freigabe')))) {
                 $Sender->Menu->AddLink('Fahrzeug-Termine', T('Fahrzeug-Termine'), 'fahrzeug-termine');
        }
    }
    public function SettingsController_FahrzeugTermine_Create($Sender) {
        $Session = Gdn::Session();
        $Sender->Title('Fahrzeug Termine');
        $Sender->AddSideMenu('plugin/FahrzeugTermine');
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.FahrzeugTermine.DCount'
        ));
        $Sender->Form->SetModel($ConfigurationModel);
        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();
            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }
        //$Sender->Render($this->GetView('ft-settings.php'));
    }
    public function PluginController_FahrzeugTermine_Create($Sender) {
        $Session = Gdn::Session();

        if (($Sender->Menu) && ((CheckPermission('Plugins.FahrzeugTermine.View')) ||  (CheckPermission('Plugins.FahrzeugTermine.Freigabe')))) {
           $Sender->ClearCssFiles();
           $Sender->AddCssFile('style.css');
		   $Sender->AddCSSFile('fz.css', 'plugins/FahrzeugTermine');
		   $Sender->AddJsFile('fahrzeugtermine.js', 'plugins/FahrzeugTermine');
           $Sender->MasterView = 'default';
           $Sender->Render('fztable', '', 'plugins/FahrzeugTermine');
		   
        }else echo Wrap(Anchor(Img('/plugins/FahrzeugTermine/design/AccessDenied.png',array('width'=>'100%'), array('title' => T('You Have No Permission To View This Page Go Back'))), '/discussions',array('target' => '_self')), 'h1');
    }
    public function OnDisable() {
	        $matchroute = '^fahrzeug-termine(/.*)?$';
	        Gdn::Router()-> DeleteRoute($matchroute); 
			$PermissionModel = Gdn::PermissionModel();
			$PermissionModel->Undefine(
              array(
                  'Plugins.FahrzeugTermine.Add',
                  'Plugins.FahrzeugTermine.View',
				  'Plugins.FahrzeugTermine.Edit',
                  'Plugins.FahrzeugTermine.Freigabe',
                  'Plugins.FahrzeugTermine.Delete'
      ));
	}
    public function Setup() {
             $matchroute = '^fahrzeug-termine(/.*)?$';
             $target = 'plugin/FahrzeugTermine$1';
             if(!Gdn::Router()->MatchRoute($matchroute))
				Gdn::Router()->SetRoute($matchroute,$target,'Internal'); 
     // Set up the db structure
    $this->Structure();
    }
	/**
   * Setup database structure for model
   */
  public function Structure() {
    $Database = Gdn::Database();
    $Construct = $Database->Structure();
    $Construct->Table('FahrzeugTermine');
    $Construct
            ->PrimaryKey('TerminID')
            ->Column('Fahrzeug', 'varchar(140)')
			->Column('Titel', 'varchar(140)')
			->Column('Von', 'datetime')
			->Column('Bis', 'datetime')
            ->Column('Verantwortlich', 'varchar(140)')
			->Column('Freischaltung', 'varchar(140)')
            ->Set();
  }
}
