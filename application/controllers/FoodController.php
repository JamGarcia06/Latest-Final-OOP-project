<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FoodController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('FoodModel');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
    }


    // =========================
    // FOOD PAGE
    // =========================

    public function index()
    {
        if (!$this->session->userdata('is_admin_logged_in')) {
            redirect('AdminController/index');
        }

        $data['foods'] = $this->FoodModel->getData();

        $data['food'] = null;

        $this->load->view('foodPage', $data);
    }


    // =========================
    // ADD FOOD
    // =========================

    public function saveData()
    {
        if (!$this->session->userdata('is_admin_logged_in')) {
            redirect('AdminController/index');
        }


        // FOOD NAME
        $this->form_validation->set_rules(
            'food_name',
            'Food Name',
            'required|trim|min_length[2]|max_length[100]'
        );


        // DESCRIPTION
        $this->form_validation->set_rules(
            'description',
            'Description',
            'required|trim|max_length[255]'
        );


        // PRICE
        $this->form_validation->set_rules(
            'price',
            'Price',
            'required|trim|numeric|greater_than[0]'
        );


        // QUANTITY
        $this->form_validation->set_rules(
            'quantity',
            'Quantity',
            'required|trim|integer|greater_than_equal_to[0]'
        );


        // STATUS
        $this->form_validation->set_rules(
            'status',
            'Status',
            'required|in_list[Available,Unavailable]'
        );


        // =========================
        // VALIDATION FAILED
        // =========================

        if ($this->form_validation->run() == FALSE)
        {
            $data['foods'] = $this->FoodModel->getData();

            $data['food'] = null;

            $this->load->view('foodPage', $data);

            return;
        }


        // =========================
        // VALIDATION PASSED
        // =========================

        $data = array(
            'food_name'   => $this->input->post('food_name', TRUE),
            'description' => $this->input->post('description', TRUE),
            'price'       => $this->input->post('price', TRUE),
            'quantity'    => $this->input->post('quantity', TRUE),
            'status'      => $this->input->post('status', TRUE)
        );


        $this->FoodModel->insertData($data);


        $this->session->set_flashdata(
            'success',
            'Food added successfully!'
        );


        redirect('FoodController/index');
    }


    // =========================
    // EDIT FOOD
    // =========================

    public function edit($id)
    {
        if (!$this->session->userdata('is_admin_logged_in')) {
            redirect('AdminController/index');
        }


        $data['food'] = $this->FoodModel->getFoodById($id);

        if (!$data['food']) {
            show_404();
        }


        $data['foods'] = $this->FoodModel->getData();

        $this->load->view('foodPage', $data);
    }


    // =========================
    // UPDATE FOOD
    // =========================

    public function updateData($id)
    {
        if (!$this->session->userdata('is_admin_logged_in')) {
            redirect('AdminController/index');
        }


        // FOOD NAME
        $this->form_validation->set_rules(
            'food_name',
            'Food Name',
            'required|trim|min_length[2]|max_length[100]'
        );


        // DESCRIPTION
        $this->form_validation->set_rules(
            'description',
            'Description',
            'required|trim|max_length[255]'
        );


        // PRICE
        $this->form_validation->set_rules(
            'price',
            'Price',
            'required|trim|numeric|greater_than[0]'
        );


        // QUANTITY
        $this->form_validation->set_rules(
            'quantity',
            'Quantity',
            'required|trim|integer|greater_than_equal_to[0]'
        );


        // STATUS
        $this->form_validation->set_rules(
            'status',
            'Status',
            'required|in_list[Available,Unavailable]'
        );


        // =========================
        // VALIDATION FAILED
        // =========================

        if ($this->form_validation->run() == FALSE)
        {
            $data['food'] = $this->FoodModel->getFoodById($id);

            $data['foods'] = $this->FoodModel->getData();

            $this->load->view('foodPage', $data);

            return;
        }


        // =========================
        // VALIDATION PASSED
        // =========================

        $data = array(
            'food_name'   => $this->input->post('food_name', TRUE),
            'description' => $this->input->post('description', TRUE),
            'price'       => $this->input->post('price', TRUE),
            'quantity'    => $this->input->post('quantity', TRUE),
            'status'      => $this->input->post('status', TRUE)
        );


        $this->FoodModel->updateFood($id, $data);


        $this->session->set_flashdata(
            'success',
            'Food updated successfully!'
        );


        redirect('FoodController/index');
    }


    // =========================
    // DELETE FOOD
    // =========================

    public function delete($id)
    {
        if (!$this->session->userdata('is_admin_logged_in')) {
            redirect('AdminController/index');
        }


        $food = $this->FoodModel->getFoodById($id);

        if (!$food) {
            show_404();
        }


        $this->FoodModel->deleteFood($id);


        $this->session->set_flashdata(
            'success',
            'Food deleted successfully!'
        );


        redirect('FoodController/index');
    }
}