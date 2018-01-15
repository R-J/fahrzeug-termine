<?php

class FahrzeugTerminePlugin extends Gdn_Plugin {

    public function base_render_before($sender) {
        if (
            ($sender->Menu) &&
            (
                checkPermission('Plugins.FahrzeugTermine.View') ||
                checkPermission('Plugins.FahrzeugTermine.Freigabe')
            )
        ) {
            $sender->Menu->addLink(
                'Fahrzeug-Termine',
                t('Fahrzeug-Termine'),
                'fahrzeug-termine'
            );
        }
    }

    public function settingsController_fahrzeugTermine_create($sender) {
        $sender->title('Fahrzeug Termine');
        $sender->addSideMenu('plugin/fahrzeugtermine');
        $sender->permission('Garden.Settings.Manage');
        $sender->Form = new Gdn_Form();
        $validation = new Gdn_Validation();
        $configurationModel = new Gdn_ConfigurationModel($validation);
        $configurationModel->setField([
            'Plugins.FahrzeugTermine.DCount'
        ]);
        $sender->Form->setModel($configurationModel);
        if ($sender->Form->authenticatedPostBack() === FALSE) {
            $sender->Form->setData($configurationModel->Data);
        } else {
            $data = $sender->Form->formValues();
            if ($sender->Form->save() !== FALSE) {
                $sender->informMessage(t("Your settings have been saved."));
            }
        }
        //$sender->Render($this->GetView('ft-settings.php'));
    }

    public function pluginController_fahrzeugTermine_create($sender) {
        if (
              ($sender->Menu) && (
                  checkPermission('Plugins.FahrzeugTermine.View') ||
                  checkPermission('Plugins.FahrzeugTermine.Freigabe')
              )
        ) {
            $sender->clearCssFiles();
            $sender->addCssFile('style.css');
            $sender->addCSSFile('fz.css', 'plugins/FahrzeugTermine');
            $sender->addJsFile('fahrzeugtermine.js', 'plugins/FahrzeugTermine');
            $sender->MasterView = 'default';
            $sender->render('fztable', '', 'plugins/FahrzeugTermine');
        } else {
            echo wrap(
                anchor(
                    img(
                        '/plugins/FahrzeugTermine/design/AccessDenied.png',
                        array('width'=>'100%'),
                        array('title' => t('You Have No Permission To View This Page Go Back'))
                    ),
                    '/discussions',
                    array('target' => '_self')
                ),
                'h1'
            );
        }
    }

    public function onDisable() {
        $matchroute = '^fahrzeug-termine(/.*)?$';
        Gdn::router()-> deleteRoute($matchroute); 
        Gdn::permissionModel()->undefine([
            'Plugins.FahrzeugTermine.Add',
            'Plugins.FahrzeugTermine.View',
            'Plugins.FahrzeugTermine.Edit',
            'Plugins.FahrzeugTermine.Freigabe',
            'Plugins.FahrzeugTermine.Delete'
        ]);
    }

    public function setup() {
        $matchroute = '^fahrzeug-termine(/.*)?$';
        $target = 'plugin/FahrzeugTermine$1';
        if (!Gdn::router()->matchRoute($matchroute)) {
            Gdn::router()->setRoute($matchroute,$target,'Internal'); 
        }
        // Set up the db structure
        $this->structure();
    }

    /**
     * Setup database structure for model
     */
    public function structure() {
        Gdn::structure()
            ->table('FahrzeugTermine');
            ->primaryKey('TerminID')
            ->column('Fahrzeug', 'varchar(140)')
            ->column('Titel', 'varchar(140)')
            ->column('Von', 'datetime')
            ->column('Bis', 'datetime')
            ->column('Verantwortlich', 'varchar(140)')
            ->column('Freischaltung', 'varchar(140)')
            ->set();
    }
}
