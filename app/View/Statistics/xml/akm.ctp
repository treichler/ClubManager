<?php  //  File: /app/View/Statistics/xml/akm.ctp
  echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<akm_meldungen>
  <akm_meldung ver_id="<?php echo Configure::read('club.akm_id') ?>">
    <allgemeines>
      <ver_id><?php echo Configure::read('club.akm_id') ?></ver_id>
      <ver_name><?php echo Configure::read('club.name') ?></ver_name>
      <adr_strasse><?php echo Configure::read('club.street') ?></adr_strasse>
      <adr_plz><?php echo Configure::read('club.postal_code') ?></adr_plz>
      <adr_ort><?php echo Configure::read('club.town') ?></adr_ort>
      <aussteller_name></aussteller_name>        <!-- Name des Programmausstellers, wenn vorhanden -->
      <aussteller_adresse></aussteller_adresse>  <!-- Anschrift des Programmausstellers, wenn vorhanden -->
    </allgemeines>

    <musikstuecke>
<?php
      foreach($events as $event):
        // skip event when track-report is not finished yet or doesn't need to be reported or customer is missing
        if ($event['Event']['tracks_checked'] == false || count($event['Track']) == 0 || $event['Event']['customer_id'] == 0) {
          continue;
        }
        foreach($event['Track'] as $track):
          $composers = array();
          foreach ($musicsheet[$track['musicsheet_id']]['Composer'] as $composer) {
            $composers[] = $composer['first_name'] . ' ' . $composer['last_name'];
          }
          $arrangers = array();
          foreach ($musicsheet[$track['musicsheet_id']]['Arranger'] as $arranger) {
            $arrangers[] = $arranger['first_name'] . ' ' . $arranger['last_name'];
          }
?>
      <musikstueck>
        <aks_id><?php echo $track['musicsheet_id'] ?></aks_id>
        <aks_akv_id><?php echo $event['Event']['id'] ?></aks_akv_id>
        <aks_status>0</aks_status>
        <aks_werknummer>0</aks_werknummer>
        <aks_titel><?php echo h($musicsheet[$track['musicsheet_id']]['Musicsheet']['title']) ?></aks_titel>
        <aks_komponist><?php echo implode(', ', $composers) ?></aks_komponist>
        <aks_arrangeur><?php echo implode(', ', $arrangers) ?></aks_arrangeur>
        <aks_anz_auffuehrungen>1</aks_anz_auffuehrungen>
        <aks_jahr><?php echo $year ?></aks_jahr>
        <aks_akm_id></aks_akm_id> <!-- AKM ID des Werkes, wird von der AKM vergeben -->
      </musikstueck>
<?php
        endforeach;
      endforeach;
      unset($event);
?>
    </musikstuecke>

    <veranstaltungen>
<?php
      foreach($events as $event):
        // skip event when track-report is not finished yet or doesn't need to be reported or customer is missing
        if ($event['Event']['tracks_checked'] == false || count($event['Track']) == 0 || $event['Event']['customer_id'] == 0) {
          continue;
        }
        $start = new DateTime($event['Event']['start']);
        $stop = new DateTime($event['Event']['stop']);
        $stop_time = $stop->format('H:i');
        if ($start->format('Ymd') != $stop->format('Ymd')) {
          $stop_time = '23:59';
        }
?>
      <veranstaltung>
        <akv_id><?php echo $event['Event']['id'] ?></akv_id>
        <akv_name><?php echo h($event['Event']['name']); ?></akv_name>
        <akv_datum><?php echo $start->format('Y-m-d'); ?></akv_datum>
        <akv_von><?php echo $start->format('H:i'); ?></akv_von>
        <akv_bis><?php echo $stop_time; ?></akv_bis>
        <akv_ort><?php echo h($event['Event']['location']); ?></akv_ort>
        <akv_veranstalter><?php echo h($event['Customer']['name']); ?></akv_veranstalter>
        <akv_veranstalter_adresse><?php echo h($event['Customer']['address']); ?></akv_veranstalter_adresse>
        <akv_kopfquote><?php echo $event['Customer']['akm_flat_rate'] ? 1 : 0 ?></akv_kopfquote>
        <akv_fremd_id></akv_fremd_id> <!-- Verweis auf die Veranstaltung in einer übergeordneten EDV -->
        <akv_status></akv_status>     <!-- Status der Verarbeitung der Veranstaltung -->
      </veranstaltung>
<?php
      endforeach;
      unset($event);
?>
    </veranstaltungen>
  </akm_meldung>
</akm_meldungen>

