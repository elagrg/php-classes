<?php 
class DetaljiRezervacije {

    public $idRezervacije;
    public $pocetakBoravka;
    public $krajBoravka;
    public $kapacitet;
    public $cijena;

    public function __construct($idRezervacije, $pocetakBoravka, $krajBoravka, $kapacitet, $cijena) {

        $this->idRezervacije = $idRezervacije;
        $this->pocetakBoravka = $pocetakBoravka;
        $this->krajBoravka = $krajBoravka;
        $this->kapacitet = $kapacitet;
        $this->cijena = number_format($cijena, 2, '.', '');

    }

    public function izracunajUkupno() {

        return number_format($this->cijena * $this->izracunajKolicinuNocenja(), 2, '.', '');

    }

    public function prikaziRasponBoravka() {

        return date('d.m.Y', strtotime($this->pocetakBoravka)) . ' - ' . date('d.m.Y', strtotime($this->krajBoravka));

    }

    public function izracunajKolicinuNocenja() {

        $pocetak = new DateTime($this->pocetakBoravka);
        $kraj = new DateTime($this->krajBoravka);
        
        $razlika = $pocetak->diff($kraj)->days;

        return $razlika;

    }
}

class Predracun {

    public $detaljiRezervacije = array();
    public $nacinPlacanja;
    public $vrijemePlacanja;

    public function __construct($nacinPlacanja) {

        $this->nacinPlacanja = $nacinPlacanja;

    }

    public function dodajDetaljeRezervacije($stavka) {

        $this->detaljiRezervacije[] = $stavka;
        $this->najbliziDatumBoravka();

    }

    public function izracunajUkupanIznos() {

        $ukupno = 0;

        foreach ($this->detaljiRezervacije as $stavka) {
            $ukupno += $stavka->izracunajUkupno();
        }

        return number_format($ukupno, 2, '.', '');

    }

    public function izracunajAkontaciju() {

        $ukupno = $this->izracunajUkupanIznos();

        $akontacija = 0.5 * $ukupno;  //ostatak iznosa će isto iznositi

        return number_format($akontacija, 2, '.', '');

    }

    public function najbliziDatumBoravka() {

        if (!empty($this->detaljiRezervacije)) {
            // uz pretpostavku da su rezervacije poredane po datumima boravka
            $startDate = strtotime($this->detaljiRezervacije[0]->pocetakBoravka);
            $this->vrijemePlacanja = date('d.m.Y', $startDate);
        }

    }
    
}

$rezervacija1 = new DetaljiRezervacije('AS-16950-a', '2022-08-08', '2022-08-15', 2, 104);
//$rezervacija2 = new DetaljiRezervacije('id2', '2024-02-29', '2024-03-02', 1, 50);

$predracun = new Predracun('Kreditnom karticom (Visa, EC/MC, Maestro)');  
$predracun->dodajDetaljeRezervacije($rezervacija1);
//$predracun->dodajDetaljeRezervacije($rezervacija2);

include 'predracun.html';

?>
