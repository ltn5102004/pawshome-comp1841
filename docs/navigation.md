# Navigation structure

```mermaid
flowchart TD
    Home[index.php<br>Home] --> Pets[pets.php<br>Find a pet]
    Home --> Shelters[shelters.php]
    Home --> Add[addpet.php<br>Rehome a pet]
    Home --> Contact[contact.php]
    Home --> Login[login.php]
    Home --> Register[register.php]
    Shelters --> Pets
    Pets --> Pet[pet.php?id=]
    Pet --> Request[requestadoption.php]
    Pet --> Edit[editpet.php]
    Pet --> Delete[deletepet.php]
    Login --> Account[myaccount.php]
    Register --> Account
    Account --> Edit
    Account --> Profile[profile.php<br>Edit details / password<br>Delete my account]
    Account --> Handler[requests.php<br>Requests for my pets<br>owners and shelter staff]
    Account --> Adopter[adopterprofile.php<br>Adopter profile]
    Pet -->|"fill in profile first"| Adopter
    Pets -->|heart button| Fav[favourite.php<br>save / unsave]
    Pet -->|heart button| Fav
    Fav --> Account
    Account --> Delete
    Login -->|admin| Admin[admin/index.php<br>Dashboard]
    Admin --> AReview[admin/approvals.php<br>Review listings]
    Admin --> APets[admin/pets.php]
    Admin --> AReq[admin/requests.php<br>all requests, supervise]
    Admin --> AShel[admin/shelters.php]
    Admin --> ASpec[admin/species.php]
    Admin --> AUsers[admin/users.php<br>Users + assign shelter staff]
    Admin --> AMsg[admin/messages.php]
    APets --> Edit
    APets --> Delete
```
Pages that need a login: favourite, profile, adopterprofile, requests, addpet, editpet, deletepet, requestadoption, myaccount. Admin pages need the admin role.

Who answers an adoption request: the **owner** of a private rehoming pet, or the **staff of the pet's shelter** (`requests.php`).
Admins see every request in `admin/requests.php` and step in when a shelter has no staff or an owner's account was deleted.
Nobody can send a request for a pet they manage themselves.

A member can create an account (register.php), edit it (profile.php) and delete it (profile.php, with the password and a confirmation).
Deleting removes the member's adopter profile, favourites, the requests they sent and their private pets with photos; pets listed for a
shelter stay. Admin accounts cannot delete themselves (the site always keeps at least one admin); another admin removes them.
