# Moodle-Django User Synchronization

## Overview
This project provides functionality to synchronize users between Moodle and Django, ensuring that users exist in both systems with consistent data.

## Features
- Fetches users from Moodle and Django.
- Identifies users who exist only in Moodle, only in Django, or in both systems.
- Creates missing users in Moodle based on Django data.
- Creates missing users in Django based on Moodle data.
- Updates Moodle users with additional identifiers from Django.
- Displays course managers and their assigned courses in Moodle.
- Displays the number of courses in each Moodle category.
- Updates student grades in Django when they are updated in Moodle.

## Installation
1. Place the provided PHP files in the appropriate Moodle plugin or custom script directory.
2. Ensure the Moodle external API (`core_user_external`) is enabled.
3. Configure the Django API URL and authentication token in the `create_django_user` and `get_django_users` functions.

## Functions
### `sync_users()`
- Retrieves user lists from both Moodle and Django.
- Categorizes users into three groups:
  - Users only in Moodle
  - Users only in Django
  - Users in both systems
- Returns categorized user lists.

### `create_moodle_user($django_user)`
- Creates a new Moodle user using Django user data.
- Sets a default password (`TempPassword123!`).
- Assigns user metadata fields such as `edeboid`, `iasid`, and `djangoid`.

### `create_django_user($moodle_user)`
- Creates a new Django user using Moodle user data.
- Uses a predefined API endpoint with authentication.
- Sends a POST request with user details.

### `get_moodle_users()`
- Retrieves all Moodle users using `core_user_external::get_users()`.

### `get_django_users()`
- Fetches all users from Django via API.
- Supports pagination to retrieve all users.

### `batch_update_moodle_users()`
- Updates Moodle user records with additional fields (`edeboid`, `iasid`, `djangoid`) fetched from Django.

### `display_course_managers()`
- Lists all users with the `manager` role.
- Shows the number of courses each manager oversees.
- Provides links to user profiles.

### `display_course_categories_and_counts()`
- Fetches all Moodle course categories.
- Displays the number of courses within each category.

### `local_django_sync_observer::grade_updated($event)`
- Listens for Moodle grade updates.
- Syncs updated grades to Django.
- Identifies the corresponding student and course before sending the update.

## Configuration
- **Django API Token:** Update the `$api_token` variable in `create_django_user` and `get_django_users`.
- **Django API URL:** Ensure the correct API endpoint is set in `get_django_users`.
- **Moodle External Services:** Ensure `core_user_external` functions are available.

## Requirements
- Moodle installation with external API access.
- Django API with user management endpoints.
- PHP with cURL enabled.

## Notes
- Users must have unique emails across both systems.
- Passwords are set to a default value when creating users in the other system.
- Error handling should be improved for production use.
- API tokens should be stored securely instead of being hardcoded.

## Future Enhancements
- Implement logging for sync operations.
- Improve exception handling for API calls.
- Secure API token storage using environment variables.
- Add CLI support for batch processing.
