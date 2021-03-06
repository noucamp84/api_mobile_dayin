<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/api/Restdata.php';

class Apirumahsakitcontroller extends Restdata{

  public function __construct(){
    parent::__construct();
    $this->load->model('mymodel');
    //mengecek token pada class Restdata, di mana jika token invalid maka akan melakukan exit
    // $this->cektoken();
    $this->ndata = isset($_GET["ndata"]) ? $this->input->get("ndata") : 0;
		$this->page = isset($_GET["page"]) ? $this->input->get("page") : 0;
  }

  function rumahsakit_get(){

    $id = (int) $this->get('id',TRUE);
    $state = (string) $this->get('state',TRUE);
    $city = (string) $this->get('city',TRUE);
    $data = (!empty($this->ndata)) ? $this->mymodel->selectrumahsakitwhere($this->ndata, $this->page, $state, $city) : $this->mymodel->selectrumahsakit($id);


    if ($data) {
      //mengembalikan respon http ok 200 dengan data dari select di atas
      $this->response($data,Restdata::HTTP_OK);
    }else {
        $this->notfound('Data Rumah Sakit Tidak Di Temukan');

    }

  }

  function anggota_post(){

    $data = [
      'npm'=>$this->post('npm',TRUE),
      'nama'=>$this->post('nama',TRUE),
      'jurusan'=>$this->post('jurusan',TRUE)
    ];

    $this->form_validation->set_rules('npm','NPM','trim|required|max_length[20]|is_unique[anggota.npm]');
    $this->form_validation->set_rules('nama','Nama','trim|required|max_length[50]');
    $this->form_validation->set_rules('jurusan','Jurusan','trim|required|max_length[20]');

    if (!$this->form_validation->run()) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      //jika berhasil di masukan maka akan di respon kembali sesuai dengan data yang di masukan
      if ($this->mymodel->insertanggota($data)) {
        $this->response($data,Restdata::HTTP_CREATED);
      }

    }

  }

  function anggota_put(){
    $id = (int) $this->get('id',TRUE);

    //mendapatkan data json yang kemudian dilakukan json decode
    $data = json_decode(file_get_contents('php://input'),TRUE);

    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('npm','NPM','trim|max_length[20]|is_unique[anggota.npm]');
    $this->form_validation->set_rules('nama','Nama','trim|max_length[50]');
    $this->form_validation->set_rules('jurusan','Jurusan','trim|max_length[20]');

    if (!$this->form_validation->run()) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      if ($this->mymodel->updateanggota($id,$data)) {
        $this->response($data,Restdata::HTTP_OK);
      }else {
        $this->badreq('gagal update anggota');
      }
    }


  }


  function anggota_delete(){
    $id = (int) $this->get('id',TRUE);

    if ($this->mymodel->deleteanggota($id)) {
      $this->response([
        'id'=>$id,
        'status'=>'deleted'
      ],Restdata::HTTP_OK);
    }else {
        $this->badreq('Failed To Delete ID '.$id);
    }

  }






}
