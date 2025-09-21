<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class InputProgram extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Input Program OPD - GeoSelaras',
            'page' => 'input-program'
        ];
        
        return view('input_program/index', $data);
    }
    
    public function create()
    {
        return view('input_program/create');
    }
    
    public function store()
    {
        // TODO: Implement program storage logic
        $validation = $this->validate([
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
            'lokasi_lat' => 'required|decimal',
            'lokasi_lng' => 'required|decimal',
            'tahun_pelaksanaan' => 'required|integer|greater_than[2020]',
            'anggaran' => 'required|integer|greater_than[0]',
            'sektor' => 'required|in_list[jalan,irigasi,pendidikan,kesehatan,ekonomi,sosial]',
            'sasaran_rpjmd' => 'required|min_length[3]'
        ]);
        
        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // TODO: Save to database
        return redirect()->to('/input-program')->with('success', 'Program berhasil ditambahkan!');
    }
    
    public function edit($id)
    {
        // TODO: Implement program editing
        $data = [
            'title' => 'Edit Program - GeoSelaras',
            'program_id' => $id
        ];
        
        return view('input_program/edit', $data);
    }
    
    public function update($id)
    {
        // TODO: Implement program update logic
        return redirect()->to('/input-program')->with('success', 'Program berhasil diperbarui!');
    }
    
    public function delete($id)
    {
        // TODO: Implement program deletion
        return redirect()->to('/input-program')->with('success', 'Program berhasil dihapus!');
    }
    
    public function validateLocation()
    {
        // TODO: Implement location validation (check for overlaps)
        $lat = $this->request->getPost('lat');
        $lng = $this->request->getPost('lng');
        
        // Placeholder for overlap detection algorithm
        $hasOverlap = false;
        
        return $this->response->setJSON([
            'valid' => !$hasOverlap,
            'message' => $hasOverlap ? 'Lokasi tumpang tindih dengan program lain!' : 'Lokasi valid'
        ]);
    }
}