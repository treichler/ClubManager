<?php
// app/Controller/Component/SmsGatewayComponent.php
App::uses('Component', 'Controller');
class SmsGatewayComponent extends Component {

  function __construct() {
    // load the model to save the sms protocol
    $this->Smsprotocol = ClassRegistry::init('Smsprotocol');
  }

  private $Smsprotocol = null;


//---------------------------------------------------------------------------------------------------------------------
//  firmensms.at
//---------------------------------------------------------------------------------------------------------------------

  public function smsGetCredit() {
    //open connection
    $ch = curl_init();

    // prepare request
    $request = "https://www.firmensms.at/gateway/senden.php?id=" .
      Configure::read('sms_gateway.username') . "&pass=" .
      Configure::read('sms_gateway.password') . "&guthaben=1";

    //set the url and other otions
    curl_setopt($ch, CURLOPT_URL, $request);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //execute the get request
    $result = curl_exec($ch);

    if ($result) {
      if (strpos($result, 'ERROR') !== false) {
        $error_code = str_replace(array('ERROR', ':', ' '), '', $result);
        $message = 'Fehler beim Abfragen des Guthabens: ' . $this->error_messages[$error_code];
      } else {
        $credit = (float) str_replace(array('Guthaben', ':', ' '), '', $result);
        $message = 'Aktuelles Guthaben: &euro;' . round($credit, 2);
      }
    } else {
      $message = 'Fehler beim Abfragen des Guthabens: ' . curl_error($ch);
    }

    //close connection
    curl_close($ch);

    return $message;
  }


  public function smsDeliver($from, $to = null, $text = null, $protocol_id = null) {
    if (!$to || !$text)
      return false;

    if (empty($from) || Configure::read('sms_gateway.useSenderId')) {
      $from = rawurlencode(Configure::read('sms_gateway.senderId'));
    } else {
      $from = '00' . str_replace(array('+', '-', ' '), '', $from);
    }

    // FIXME the symbol '§' is not in the list since there are problems excluding the symbol '°'.
    $sms_chars = "a-zA-Z0-9 \\r\\n\\!\"'\\$%&\\/\\(\\)\\=\\?\\+#\\*,.\\-;\\:_ßöäüÖÄÜ@~\\[\\]\\{\\}\\|\\^\\<\\>";
//    $sms_chars = "a-zA-Z0-9 \\r\\n\\!\"'§\\$%&\\/\\(\\)\\=\\?\\+#\\*,.\\-;\\:_ßöäüÖÄÜ@~\\[\\]\\{\\}\\|\\^\\<\\>";

    $message = rawurlencode( utf8_decode( preg_replace("/[^" . $sms_chars . "]+/", "", $text) ) );

    //open connection to sms-gateway server
    $ch = curl_init();

    $error = 0;
    foreach ($to as $t) {
      // prepare the request
      $request = "https://www.firmensms.at/gateway/senden.php?xml=1" .
        "&id="            . Configure::read('sms_gateway.username') .
        "&pass="          . Configure::read('sms_gateway.password') .
        "&absender="      . $from .
        "&gueltigkeit="   . Configure::read('sms_gateway.validity') .
        "&test="          . (Configure::read('sms_gateway.testMode') ? 1 : 0) .
        "&route="         . 5 .
        "&statusbericht=" . 1 .
        "&nummer=00"      . str_replace(array('+', '-', ' '), '', $t['phone_nr']) .
        "&text="          . $message;

      //set the url
      curl_setopt($ch, CURLOPT_URL, $request);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      //execute the get request
      $result = curl_exec($ch);

      if ($result) {
        // xml-parse result and save msgid and profile_id
        $xml = Xml::build($result);
        $this->Smsprotocol->create();
        $this->Smsprotocol->save(array('Smsprotocol' => array(
          'contactprotocol_id'  => $protocol_id,
          'profile_id'          => $t['profile_id'],
          'msgid'               => ((string) ($xml->msgid)),
          'phone'               => $t['phone_nr'],
          'report'              => $this->error_message($xml->error),
          'costs'               => ((float) ($xml->transaktionskosten)),
        )));
        if (((string) ($xml->error))) {
          $error ++;
        }
      } else {
        $error ++;
      }
    }

    //close connection
    curl_close($ch);

    // short report
    $text = 'Die Nachricht wurde an das SMS-Gateway &uuml;bertragen';
    if ($error) {
      $text = 'Bei ' . $error . ' von ' . count($to) . ' SMS gab es Probleme bei der &Uuml;bertragung.';
    }

    return array('error' => $error, 'text' => $text);
  }


  public function smsCallback($data = null) {
    // check if neccessary parameters are available
    if (!$data || !$data['tel'] || !$data['status'] || !$data['msgid'])
      return 'parameter missing';

    // try to get the database entry
    $smsprotocol = $this->Smsprotocol->find('first', array(
      'conditions' => array('msgid' => $data['msgid'])
    ));
    if (empty($smsprotocol['Smsprotocol']['id']))
      return 'no entry found';
    // TODO check if phone numbers are correlating
    // return 'phone number mismatch';

    // write status
    $smsprotocol['Smsprotocol']['status'] = $data['status'];
    $this->Smsprotocol->save($smsprotocol);

    return 'OK';
  }


  private function error_message ($index) {
    $msg = array(
      0  => 'Übertragung OK.',
      1  => 'keine ID.',
      2  => 'kein Passwort.',
      3  => 'keine Nachricht.',
      4  => 'keine Nummer.',
      5  => 'kein Absender.',
      6  => 'kein Flash-Typ.',
      7  => 'falsches Passwort.',
      8  => 'kein Business-Account.',
      9  => 'SMS konnte nicht angenommen / verschickt werden. Der Support wurde über den Fehler automatisch informiert und wird Sie gegebenenfalls kontaktieren.',
      10 => 'Absenderkennung ungültig.',
      11 => 'Sie haben die SPAM-SMS Einstellung aktiviert und diese SMS wurde als SPAM-SMS erkannt und nicht versendet',
      12 => 'SMS wurde innerhalb der von Ihnen definierten SMS-Pause eingeliefert. Diese SMS wird nach der Pause an den Empfänger zugestellt bzw. wird die SMS bei Angabe von dem Parameter "datum" erst zu diesem Zeitpunkt zugestellt.',
      13 => 'Die SMS konnte nicht versendet werden. Bitte versuchen Sie den SMS-Versand erneut. Sollte er noch immer nicht funktionieren, kontaktieren Sie bitte den Support.',
      14 => 'Sie senden die SMS an eine ungültige Handynummer.',
      15 => 'IP Lock ist gesetzt und die IP Adresse wurde nicht zum Versenden von SMS freigegeben. Siehe "HTTP-Schnittstelle" in Ihrem firmensms.at Kundenbereich.',
      16 => 'Der Parameter "data" wurde bei einem Versand über die XML-Schnittstelle nicht übergeben.',
    );
    if (isset($msg[(int)$index])) {
      return $msg[(int)$index];
    }
    return 'Unbekannter Fehler: ' . $index;
  }

}

