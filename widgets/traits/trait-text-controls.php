<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

trait Text_Controls {

    protected function register_text_controls() {
        $this->start_controls_section( 'section_text', [
            'label' => 'Text Labels',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $texts = [
            'step1_title'   => [ 'Step 1 Title', 'Choose a Service' ],
            'step2_title'   => [ 'Step 2 Title', 'Choose a Date' ],
            'step3_title'   => [ 'Step 3 Title', 'Choose a Time' ],
            'step4_title'   => [ 'Step 4 Title', 'Your Details' ],
            'step1_btn'     => [ 'Step 1 Button', 'Choose Date →' ],
            'step2_btn'     => [ 'Step 2 Button', 'Choose Time →' ],
            'step3_btn'     => [ 'Step 3 Button', 'Your Details →' ],
            'submit_btn'    => [ 'Submit Button', 'Confirm Booking' ],
            'back_btn'      => [ 'Back Button', '← Back' ],
            'book_again'    => [ 'Book Again Button', 'Book Another Appointment' ],
            'success_title' => [ 'Success Title', "You're all booked!" ],
            'success_text'  => [ 'Success Text', 'A confirmation email has been sent to' ],
        ];

        foreach ( $texts as $key => $data ) {
            $this->add_control( $key, [
                'label'   => $data[0],
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => $data[1],
            ] );
        }

        $this->add_control( 'step_labels_heading', [
            'label' => 'Step Labels',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $step_labels = [
            'step_label_1' => [ 'Step 1 Label', 'Service' ],
            'step_label_2' => [ 'Step 2 Label', 'Date' ],
            'step_label_3' => [ 'Step 3 Label', 'Time' ],
            'step_label_4' => [ 'Step 4 Label', 'Details' ],
        ];
        foreach ( $step_labels as $key => $data ) {
            $this->add_control( $key, [
                'label'   => $data[0],
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => $data[1],
            ] );
        }

        $this->add_control( 'summary_heading', [
            'label' => 'Summary Defaults',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $summaries = [
            'summary_service' => [ 'No Service Text', 'No service selected' ],
            'summary_date'    => [ 'No Date Text', 'No date selected' ],
            'summary_time'    => [ 'No Time Text', 'No time selected' ],
        ];
        foreach ( $summaries as $key => $data ) {
            $this->add_control( $key, [
                'label'   => $data[0],
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => $data[1],
            ] );
        }

        $this->add_control( 'fields_heading', [
            'label' => 'Form Fields',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $fields = [
            'field_name_label'       => [ 'Name Label', 'Full Name' ],
            'field_email_label'      => [ 'Email Label', 'Email Address' ],
            'field_phone_label'      => [ 'Phone Label', 'Phone Number' ],
            'field_notes_label'      => [ 'Notes Label', 'Special Requests / Notes' ],
            'field_name_placeholder' => [ 'Name Placeholder', 'Jane Smith' ],
            'field_email_placeholder'=> [ 'Email Placeholder', 'jane@example.com' ],
            'field_phone_placeholder'=> [ 'Phone Placeholder', '+1 (555) 000-0000' ],
            'field_notes_placeholder'=> [ 'Notes Placeholder', 'Any allergies, preferences or special requests...' ],
            'field_required_mark'    => [ 'Required Mark', '*' ],
        ];
        foreach ( $fields as $key => $data ) {
            $this->add_control( $key, [
                'label'   => $data[0],
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => $data[1],
            ] );
        }

        $this->add_control( 'summary_box_heading', [
            'label' => 'Booking Summary Labels',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $box_labels = [
            'bsb_service'      => [ 'Service Label', 'Service' ],
            'bsb_date'         => [ 'Date Label', 'Date' ],
            'bsb_time'         => [ 'Time Label', 'Time' ],
            'bsb_price'        => [ 'Price Label', 'Price' ],
        ];
        foreach ( $box_labels as $key => $data ) {
            $this->add_control( $key, [
                'label'   => $data[0],
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => $data[1],
            ] );
        }

        $this->add_control( 'messages_heading', [
            'label' => 'Messages',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $messages = [
            'msg_loading_services' => [ 'Loading Services', 'Loading services...' ],
            'msg_loading_slots'    => [ 'Loading Slots', 'Loading available times...' ],
            'msg_empty_services'   => [ 'Empty Services', 'No services available. Please check back later.' ],
            'msg_empty_slots'      => [ 'Empty Slots', 'No available slots for this date. Choose another.' ],
            'msg_error_name'       => [ 'Error — Name', 'Please enter your full name.' ],
            'msg_error_email'      => [ 'Error — Email', 'Please enter a valid email.' ],
            'msg_error_network'    => [ 'Error — Network', 'Network error. Check your connection.' ],
            'msg_error_slot_taken' => [ 'Error — Slot Taken', 'This slot was just taken. Please choose another.' ],
            'msg_submitting'       => [ 'Submitting Text', 'Submitting...' ],
            'slot_remaining'       => [ 'Slots Remaining Text', 'left' ],
            'slot_full'            => [ 'Slot Full Text', 'Full' ],
            'free_label'           => [ 'Free Label (price=0)', 'Free' ],
        ];
        foreach ( $messages as $key => $data ) {
            $this->add_control( $key, [
                'label'   => $data[0],
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => $data[1],
            ] );
        }

        $this->end_controls_section();
    }
}
