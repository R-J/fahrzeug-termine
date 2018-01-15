<?php

class FahrzeugTermineModel extends Gdn_Model{

    public function __construct() {
        parent::__construct('FahrzeugTermin');
    }

     /**
     * Get all appointments from database table.
     *
     * @param Limit limits the number of results
     * @param Offset
     * @param SortOrder change the order of the result, Allowed options are:
     *                                                    asc.
     *                                                    dsc.
     *
     * @return array appointments.
     */
    public function getFahrzeugTermine($Limit, $Offset, $SortOrder){
        // Das Model bietet die  Standard-Funktion get() an.
        // Dein Model muss das nicht nochmal implementieren
    }
     /**
     * Counts all appointments in database table.
     *
     * @return int.
     */
    public function GetFahrzeugCount() {
        // class.model.php hat auch schon eine getCount Methode
    }
     /**
     * Check if the user can delete appointments, returns true or false.
     * Users are always allowed to delete their own.
     *
     * @return bool.
     */
    public function canDelete($termin){
        // Ein termin hat keine UserID...
        $session = Gdn::session();
        if ($session->checkPermission('Plugins.FahrzeugTermine.Delete') || $termin->UserID == $session->UserID) {
            // User can delete appointment
            return true;
        }
    else
    return false;
    }
    /**
     * Check if the user can approve appointments, returns true or false.
     *
     * @return bool.
     */
    public function canApprove(){
    $session = Gdn::session();
        if ($session->checkPermission('Plugins.FahrzeugTermine.Freigabe')){
            // User can approve appointment
            return true;
        }
    else
    return false;
    }
    /**
     * Check if the user can edit appointments, returns true or false.
     * Users are always allowed to edit their own, except the appointment is already approved.
     *
     * @return bool.
     */
    public function canEdit($Termin){
    $session = Gdn::session();
        if ($session->checkPermission('Plugins.FahrzeugTermine.Edit') || $Termin->UserID == $session->UserID and $Termin->Freischaltung == 0){
            // User can edit appointment
            return true;
        }
    else
    return false;
    }
    /**
     * Check if the user can add appointments, returns true or false.
     *
     * @return bool.
     */
    public function canAdd(){
    $session = Gdn::session();
        
        if ($session->checkPermission('Plugins.FahrzeugTermine.Add')) {
            // User can add appointment
            return true;
        }
    else
    return false;
    }
    /**
     * ToDo, tryed to delete a appointment via id.
     *
     * @return void.
     */
    public function deleteID($terminID, $options = array()) {
        // Get appointment
        $termin = $this->getID($terminID);
        if ($termin) {
            // Log
            $Log = val('Log', $Options);
            if ($Log) {
                LogModel::insert($Log, 'Termin', $Termin);
            }
            parent::deleteID($terminID, $options)
        }
    }
}




  
