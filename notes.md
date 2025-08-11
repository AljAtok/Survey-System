

# 1. PRIA version 10.24.22 Documentation

Author     : Jhun Baria \
Description: DIPMC Form is a system for DP clean and dirty area checklist for standards

- [1. PRIA version 10.24.22 Documentation](#1-pria-version-102422-documentation)
	- [1.1. TODO LIST (Change request 11.10.22)](#11-todo-list-change-request-111022)
		- [1.1.1. Others](#111-others)


## 1.1. TODO LIST (Change request 11.10.22)

- [x] Enable Decimal in number fields
- [x] Add Standard validation checking in fields
- [x] Add new fields
- [x] Adjust new order of the fields
- [x] Separate make creating dirty area and clean area form at the start via modal \ with just Business Center, Dressing Plant, Farm, Trip Num, Truck Vol, ALW as initial fields for creation
- [x] Add Form Code for easier identification of the linked forms and created forms
- [x] Create per Category page with stepper
- [x] Create transaction date and transaction time
- [x] Added transaction date and transaction time in transaction table

### 1.1.1. Others

- [ ] Run Mysql Script Below for additional table fields
```mysql
	ALTER TABLE `form_field_tbl`  ADD `has_remark` INT NOT NULL  AFTER `is_readonly`,  ADD `standard_rule` VARCHAR(256) NOT NULL  AFTER `has_remark`;
	ALTER TABLE `form_header_tbl`  ADD `form_header_code` VARCHAR(52) NOT NULL  AFTER `form_id`;
```



— upon making a form, it automatically creates a draft for dirty & clean. (much better if there is an option which form you want to accomplish)
— prompts non-compliant items
— cannot be saved if there are non-compliant items



ALTER TABLE `form_header_details_tbl`  ADD `is_standard` INT NOT NULL COMMENT '0 = no, 1 = yes, 2 = no standard'  AFTER `form_header_detail_field_answer`;
ALTER TABLE `form_header_details_tbl`  ADD `remarks` VARCHAR(256) NOT NULL  AFTER `is_standard`;
ALTER TABLE `form_header_tbl` CHANGE `form_header_status` `form_header_status` INT(11) NOT NULL COMMENT '0 = deactivated , 1 = posted, 2 = drafted, 3 = for summary';


stripslashes();