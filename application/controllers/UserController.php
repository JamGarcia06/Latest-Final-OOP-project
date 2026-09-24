<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('session');

		$this->load->model('UserModel');
		$this->load->model('FoodModel');
		$this->load->model('ReservationModel');
	}


	public function index()
{
    if(!$this->session->userdata('is_user_logged_in'))
    {
        $this->load->view('userLogin');
        return;
    }

    $data['foods'] = $this->FoodModel->getData();

    $data['admin'] = $this->db->get('admin_acc')->row();

    $this->load->view('userPage', $data);
}

	public function login()
{
    $email = $this->input->post('email');
    $password = $this->input->post('password');

    $user = $this->UserModel->login($email, $password);

    if($user)
    {
        $session_data = [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_user_logged_in' => TRUE
        ];

        $this->session->set_userdata($session_data);

        redirect('UserController/index');
    }
    else
    {
        $data['error'] = "Invalid login";
        $this->load->view('userLogin', $data);
    }
}


	public function logout()
{
    $this->session->sess_destroy();

    redirect('UserController/login');
}

		public function reserve($food_id)
{
    if(!$this->session->userdata('is_user_logged_in'))
    {
        redirect('UserController/index');
    }

    $data['foods'] = $this->FoodModel->getData();
    $data['food'] = $this->FoodModel->getFood($food_id);

    $data['admin'] = $this->db->get('admin_acc')->row();

    $this->load->view('userPage', $data);
}


public function saveReservation()
{
    if(!$this->session->userdata('is_user_logged_in'))
    {
        redirect('UserController/index');
    }

    $food_id = $this->input->post('food_id');
    $quantity = $this->input->post('quantity');

    // Make sure quantity is treated as a number
    $quantity = (int)$quantity;

    $food = $this->FoodModel->getFood($food_id);

    if(!$food)
    {
        show_error('Food not found.');
        return;
    }

    if($quantity <= 0)
    {
        show_error('Invalid quantity.');
        return;
    }

    if($quantity > $food->quantity)
    {
        $data['available'] = $food->quantity;
        $data['food_name'] = $food->food_name;
        $data['food_id'] = $food_id;

        $this->load->view('reservationFailed', $data);
        return;
    }

    $data = [
        'user_id' => $this->session->userdata('user_id'),
        'food_id' => $food_id,
        'quantity' => $quantity,
        'status' => 'Pending',
        'reservation_time' => date('Y-m-d H:i:s')
    ];

    if($this->ReservationModel->insertData($data))
    {
        redirect('UserController/myReservations');
    }
    else
    {
        show_error('Error saving reservation.');
    }
}

	public function myReservations()
	{
		if(!$this->session->userdata('is_user_logged_in'))
		{
			redirect('UserController/index');
		}


		$user_id = $this->session->userdata('user_id');

		$data['reservations'] =
		$this->ReservationModel->getUserReservations($user_id);

		$this->load->view('myReservations', $data);
	}

	public function saveData()
{
    $this->load->library('form_validation');

    $this->form_validation->set_rules(
        'food_name',
        'Food Name',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'description',
        'Description',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'price',
        'Price',
        'required|numeric|greater_than[0]'
    );

    $this->form_validation->set_rules(
        'quantity',
        'Quantity',
        'required|integer|greater_than_equal_to[0]'
    );

    $this->form_validation->set_rules(
        'status',
        'Status',
        'required|in_list[Available,Unavailable]'
    );


    if($this->form_validation->run() == FALSE)
    {
        $data['foods'] = $this->FoodModel->getData();
        $data['food'] = null;
        $data['error'] = validation_errors();

        $this->load->view('foodPage', $data);

        return;
    }


    $data = [
        'food_name' => $this->input->post('food_name'),
        'description' => $this->input->post('description'),
        'price' => $this->input->post('price'),
        'quantity' => $this->input->post('quantity'),
        'status' => $this->input->post('status')
    ];

    $response = $this->FoodModel->insertData($data);

    if($response)
    {
        redirect('FoodController/index', 'refresh');
    }
    else
    {
        show_error('Error saving food');
    }
}

	public function updateData($id)
{
    $this->load->library('form_validation');

    $this->form_validation->set_rules(
        'food_name',
        'Food Name',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'description',
        'Description',
        'required|trim'
    );

    $this->form_validation->set_rules(
        'price',
        'Price',
        'required|numeric|greater_than[0]'
    );

    $this->form_validation->set_rules(
        'quantity',
        'Quantity',
        'required|integer|greater_than_equal_to[0]'
    );

    $this->form_validation->set_rules(
        'status',
        'Status',
        'required|in_list[Available,Unavailable]'
    );


    if($this->form_validation->run() == FALSE)
    {
        $data['foods'] = $this->FoodModel->getData();
        $data['food'] = $this->FoodModel->getFood($id);
        $data['error'] = validation_errors();

        $this->load->view('foodPage', $data);

        return;
    }


    $data = [
        'food_name' => $this->input->post('food_name'),
        'description' => $this->input->post('description'),
        'price' => $this->input->post('price'),
        'quantity' => $this->input->post('quantity'),
        'status' => $this->input->post('status')
    ];

    $response = $this->FoodModel->updateData($id, $data);

    if($response)
    {
        redirect('FoodController/index', 'refresh');
    }
    else
    {
        show_error('Error updating food');
    }
}

}
?>