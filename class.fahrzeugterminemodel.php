<?php if(!defined('APPLICATION')) exit();

class FahrzeugTermineModel extends Gdn_Model{
	 /**
     * Get all appointments from database table.
     *
	 * @param Limit limits the number of results
	 * @param Offset
	 * @param SortOrder change the order of the result, Allowed options are:
	 *													asc.
	 *													dsc.
	 *
     * @return array appointments.
     */
	public function GetFahrzeugTermine($Limit, $Offset, $SortOrder){
		$SQL = Gdn::SQL();
     $FahrzeugTermineModel = new Gdn_Model('FahrzeugTermine');
        $SQL = $FahrzeugTermineModel->SQL
        ->Select('*')
        ->From('FahrzeugTermine u');
       return $SQL->Limit($Limit, $Offset)->Get();;
	}
	 /**
     * Counts all appointments in database table.
     *
     * @return int.
     */
	public function GetFahrzeugCount(){
		$result = Gdn::SQL()
        ->Select('*')
        ->From('FahrzeugTermine')
		->get()
		->count();
       return $result;
    }
	 /**
     * Check if the user can delete appointments, returns true or false.
	 * Users are always allowed to delete their own.
     *
     * @return bool.
     */
	public function canDelete($Termin){
	$session = Gdn::session();
        if ($session->checkPermission('Plugins.FahrzeugTermine.Delete') || $Termin->UserID == $session->UserID) {
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
	public function deleteID($TerminID, $Options = array()) {
	    if (!Gdn::session()->validateTransientKey($transientKey)) {
        throw permissionException();
    }
        // Get appointment
        $Termin = $this->getID($TerminID);
        if ($TerminID) {
            // Log
            $Log = val('Log', $Options);
            if ($Log) {
                LogModel::insert($Log, 'Termin', $Termin);
            }
            // Delete appointment from database
           $this->SQL->delete('FahrzeugTermine', array('TerminID' => val('TerminID', $options, 0)));
        }
    }
	 /** by R_J
     * Delete entry from the table.
     *
     * 
     *
     * @param SettingsController $sender Instance of the calling class.
     * @param array $options Allowed options are
     *                       PrimaryKeyValue: The appointment ID.
     *
     * @return void.
     */
	public function delete($sender, $options) {
    // Check for valid TransientKey before deleting.
    if (Gdn::session()->validateTransientKey($sender->Request->get('tk'))) {
		/* Logging
		$Termin = $this->getID($TerminID);
		$Log = val('Log', $Options);
            if ($Log) {
                LogModel::insert($Log, 'Termin', $Termin);
            }
		*/
		Gdn::sql()->delete('FahrzeugTermine', array('TerminID' => val('TerminID', $options, 0)));
		$Sender->InformMessage('Termin gelöscht!');
        redirect($this->indexLink);
			}
     }
}




  
