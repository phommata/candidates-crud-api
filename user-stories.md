# Interview User Stories

## Get Candidate
Already created for you
 

## List Candidates
* As a candidate API user,
* I want to be able to get a list of all of the candidates
* So that I can show a table of candidates to a hiring manager
 
 
### Acceptance Criteria
* When I GET `/candidate`
* If I have permissions to list all of the candidates
* Then I receive a list of candidates
  * With their id, first name, last name, email, and creation date/time
 

## Filter and Sort Candidate List
* As a candidate API user,
* I want to be able to filter and sort the candidate list
* So that my users can easily find the candidates they’re looking of
 
### Acceptance Criteria
* When I GET `/candidate`
* Then I receive a list of candidates


* If I supply a date range in as a query param (from-date=<date / time>) and/or (to-date=<date / time>)
* Then I should get a list of candidates filtered by that date range

 
* If I supply a sort column (sort) of
  “id”, “first_name”, “last_name”, “email”, or “created_at” 
  and a direction (dir) of ascending (asc) or descending (desc)
* Then I receive the list sorted by the “sort” with the direction of ascending or descending.
 
 
## Update Candidate Email and Name
 
* As a candidate API user,
* I want to be able to edit a candidate’s email and name
* So that we can keep the data correct
 
### Acceptance Criteria
* When I send an update request to `/candidate/{id}` 
* If I have permission to update candidates
* Then the candidate with the corresponding ID is updated
 
The body of the request is JSON. The user cannot overwrite the id or the creation date. They can update all of the other fields. The date must be valid. The email address must be valid email syntax.
 
The response is a JSON object containing the new candidate’s data
 
 
## Delete Candidate
* As a candidate API user,
* I want to be able to soft delete a candidate
* So that I am no longer shown the candidates but we still keep the data in case of an accident
 
### Acceptance Criteria
* When I send a DELETE request to `/candidate/{id}` 
* If I have permission to delete candidates
* Then I no longer see the candidate in any of the APIs
 
The response is just a 200 OKAY
 
 
## Create Candidate
* As a candidate API user,
* I want to be able to create a candidate
* So that we can grow our database of candidates
 
### Acceptance Criteria
* When I send a POST request to `/candidate/` 
* If I have permission to create candidates
  * And if the candidate's email does not already exist
* Then a candidate is created
* When a candidate's email exists 
* Then return an error with an appropriate HTTP status
 
The body of the request is JSON. The user cannot overwrite the id or the creation date.
 
The response is a JSON object containing the new candidate’s data

### Note
For the sake of this exercise, it will just return a new object. We may do more when we save this data.

