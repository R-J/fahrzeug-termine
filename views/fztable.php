<?php if(!defined('APPLICATION')) die();
 $Session = Gdn::Session();

 if (!((CheckPermission('Plugins.FahrzeugTermine.View'))or (CheckPermission('Plugins.FahrzeugTermine.Freigabe')))) 
	 throw permissionException();
     $RequestArgs = Gdn::Request()->GetRequestArguments();
     $SortOrder = GetValue('sort',$RequestArgs['get']);
    if (!$SortOrder) 
         $SortOrder = 'asc';
    $Limit = "10";

   if (!is_numeric($Limit) || $Limit < 0)
       $Limit = 20;

     $MLUrl = $this->SelfUrl; 
     
     $Arg = $MLUrl;
     $MColUrl = 'fahrzeug-termine/p1';
     
     $Page = 1;
    
    if (preg_match("|p(\d+)|", $Arg,$matches)){
                  $Page = $matches[1];
    }
    
    $SortOrder = strtolower($SortOrder);
    if (($SortOrder == 'asc') ||(!$SortOrder)) {
            $NewSort = 'desc';
            $SortOrder = 'asc';
    } else {
            $NewSort = 'asc';
            $SortOrder = 'desc';
            }
    $Alt = FALSE;
    $Sender->Offset = ($Page * $Limit) - $Limit ;
    $Offset = $Sender->Offset;
  

   $this->FahrzeugTermine = FahrzeugTermineModel::GetFahrzeugTermine($Limit, $Offset,$SortOrder);
   $MemNumRows = FahrzeugTermineModel::GetFahrzeugCount();
   $mydata = $this->FahrzeugTermine;
        $PagerFactory = new Gdn_PagerFactory();
        $Sender->Pager = $PagerFactory->GetPager('Pager', $this);
        $Sender->Pager->MoreCode = '>';
        $Sender->Pager->LessCode = '<';
        $Sender->Pager->ClientID = 'Pager';
     
        $Sender->Pager->Configure(
        $Sender->Offset,
        $Limit,
        $MemNumRows,
         'fahrzeug-termine' . '/{Page}' . '&sort=' . $SortOrder . '&ufield=' .$UserField
        );

  echo $Sender->Pager->ToString('more');
  if(!empty($mydata))
  {
	  ?>
<h1><?php echo T('Fahrzeug Termine'); ?></h1>
<div id="Table">
<table id="Fahrzeug-Termine" class="AltColumns" style="width: 100%;">
   <thead>
      <tr class="Info">
	  
       <?php 
		echo '<th>' . Anchor(T('ID'),$MColUrl. '&ufield=' . 'ID' . '&sort=' . $NewSort ).'</th>';
		echo '<th>' . Anchor(T('Fahrzeug'),$MColUrl. '&ufield=' . 'Fahrzeug' . '&sort=' . $NewSort ).'</th>';
		echo '<th>' . Anchor(T('Titel'),$MColUrl. '&ufield=' . 'Titel' . '&sort=' . $NewSort ).'</th>';
		echo '<th>' . Anchor(T('Von'),$MColUrl. '&ufield=' . 'Von' . '&sort=' . $NewSort ).'</th>';
		echo '<th>' . Anchor(T('Bis'),$MColUrl. '&ufield=' . 'Bis' . '&sort=' . $NewSort ).'</th>';
		echo '<th>' . Anchor(T('Verantwortlich'),$MColUrl. '&ufield=' . 'Verantwortlich' . '&sort=' . $NewSort ).'</th>';
		echo '<th>' . Anchor(T('Freischaltung'),$MColUrl. '&ufield=' . 'Freischaltung' . '&sort=' . $NewSort ).'</th>';
		echo '<th>Aktion</th>';
       echo   '</tr></thead><tbody>';
	  
   foreach ($mydata as $Termin) {
     $Alt = $Alt ? FALSE : TRUE;
     ?>
     <tr<?php echo $Alt ? ' class="Alt"' : ''; ?>>
      <?php  
	  if($Termin->Freischaltung == 0)
		  $Freischaltung = "ohne";
	  else{
		  $user ="" ;
			$Freischaltung = userAnchor(Gdn::userModel()->getID($Termin->Freischaltung), 'Username');
	  }
        echo  '<td>' . $Termin->FahrzeugTerminID . '</td>';
		echo  '<td>' . $Termin->Fahrzeug . '</td>';
		echo  '<td>' . $Termin->Titel . '</td>';
		echo  '<td>' . strftime(t('FahrzeugTermine.DateFormat', '%e.%m.%y %H:%M'), strtotime($Termin->Von )) . '</td>';
		echo  '<td>' . strftime(t('FahrzeugTermine.DateFormat', '%e.%m.%y %H:%M'), strtotime($Termin->Bis )) . '</td>';
		echo  '<td>' . userAnchor(Gdn::userModel()->getID($Termin->UserID), 'Username') . '</td>';
		echo  '<td>' . $Freischaltung . '</td>';
		echo  '<td>';
		echo "<a class=\"Popup Button Action\" href=\"#termin_" . $Termin->FahrzeugTerminID . "\">Details</a>";
		$user = Gdn::userModel()->getID($Termin->UserID);
		?>
		<dd>
		 <div id="termin_<?php echo $Termin->FahrzeugTerminID ?>">
		<h2><?= htmlEsc($Termin->Titel) ?></h2>
        <div class="Item-Header DiscussionHeader">
          <div class="AuthorWrap">
            <span class="Author">
            <?php
              if ($userPhotoFirst) {
                echo userPhoto($user);
                echo t('Verantwortlich: ').userAnchor($user, 'Username');
              } else {
                echo t('Verantwortlich: ').userAnchor($user, 'Username');
                echo userPhoto($user);
              }
            ?>
            </span>
            <span class="AuthorInfo">
            <?php
              echo wrapIf(htmlEsc(val('Title', $user)), 'span', ['class' => 'MItem AuthorTitle']);
              echo wrapIf(htmlEsc(val('Location', $user)), 'span', ['class' => 'MItem AuthorLocation']);
            ?>
            </span>
        </div>
        <div class="Meta DiscussionMeta">
          <span class="MItem DateEvent">
            <?= sprintf(t('Von %s'), strftime(t('FahrzeugTermine.DateFormat', '%a, %e. %B %Y - %H:%M'), strtotime($Termin->Von ))) ?>
          </span>
          <span class="MItem DateCreated">
            <?= sprintf(t('Bis %s'), strftime(t('FahrzeugTermine.DateFormat', '%a, %e. %B %Y - %H:%M'), strtotime($Termin->Bis ))) ?>
          </span>
        </div>
      </div>
      <div class="EventBody"></div>
      <div><?php
		if (FahrzeugTermineModel::canApprove() and $Termin->Freischaltung == 0){
        echo Anchor(t('Freigeben'), 'fahrzeug-termine/approve/'.$Termin->FahrzeugTerminID.'&tk='.$Session->TransientKey(), ['class' => 'Button Primary Action NewDiscussion']);
		}
		elseif($Termin->Freischaltung != 0){
			echo "Eintrag wurde Freigegeben und kann nicht mehr bearbeitet werden.";
		}

	  ?></div>
    </div>
		</div>
		</dd>
		<?php
		if (FahrzeugTermineModel::canEdit($Termin)) {
		echo Anchor(t('Edit'),'fahrzeug-termine/edit/'.$Termin->FahrzeugTerminID.'/'.$Session->TransientKey(),['class' => 'Button SmallButton  Edit']). " " ;
		}

        if (FahrzeugTermineModel::canDelete($Termin)) {
        echo Anchor(t('Delete'), 'fahrzeug-termine/delete/'.$Termin->FahrzeugTerminID.'&tk='.$Session->TransientKey(), ['class' => 'Button SmallButton Cancel PopConfirm']);
		}
        echo '</td></tr>';
		}
		echo  '</tbody></table></div>';
		echo $Sender->Pager->ToString('more');
  }
  else {
	  echo "<div class=\"Info\">Keine Fahrzeugtermine vorhanden.</div>";
  }
		if (FahrzeugTermineModel::canAdd()) {
		echo '</br> <a href=\'/fahrzeug-termine/add\' style=\'width:20%; min-width:250px;margin:auto\' class=\'Button Primary Action NewDiscussion BigButton\'>Fahrzeug-/Materialtermin hinzufügen</a>';
		}