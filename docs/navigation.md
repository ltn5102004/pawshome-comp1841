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
    Login --> Forgot[forgotpassword.php<br>Forgot your password?]
    Forgot -->|emailed one-time link| Reset[resetpassword.php<br>Choose a new password]
    Reset --> Login
    Account --> Withdraw[withdrawrequest.php<br>Withdraw my request]
    Account --> Logout[logout.php<br>Log out - POST button]
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


## Sitemap (grouped by who can open the page) - Figure 1 in the report

Rendered with Mermaid (Mermaid, no date. Flowcharts syntax. https://mermaid.js.org/syntax/flowchart.html).

```mermaid
flowchart LR
    Home(["index.php<br/>Home"])
    subgraph Public["Public pages (no login)"]
        direction TB
        Pets["pets.php<br/>Find a pet + filters"] --> Pet["pet.php?id=<br/>Pet details (JOIN)"]
        Shelters["shelters.php<br/>Shelters"]
        Contact["contact.php<br/>Contact the admin"]
        Register["register.php<br/>Create account"]
        Login["login.php<br/>Log in"] --> Forgot["forgotpassword.php"] -->|"emailed link"| Reset["resetpassword.php"]
    end
    subgraph Member["Logged-in members (owners, adopters, shelter staff)"]
        direction TB
        Account["myaccount.php<br/>My account"]
        Add["addpet.php<br/>Rehome a pet"]
        Edit["editpet.php / deletepet.php<br/>own pets only"]
        Req["requestadoption.php<br/>Send a request"]
        Withdraw["withdrawrequest.php<br/>Withdraw my request"]
        Handle["requests.php<br/>Requests for my pets"]
        Fav["favourite.php<br/>Save a pet"]
        Prof["profile.php / adopterprofile.php<br/>Details, password, adopter profile"]
        Out["logout.php<br/>Log out (POST)"]
    end
    subgraph Admin["Admin area (admin role)"]
        direction TB
        Dash["admin/index.php<br/>Dashboard"]
        Appr["admin/approvals.php<br/>Review listings"]
        APets["admin/pets.php"]
        AReq["admin/requests.php"]
        AShel["admin/shelters.php"]
        ASpec["admin/species.php"]
        AUsers["admin/users.php"]
        AMsg["admin/messages.php"]
    end
    Home --> Pets & Shelters & Contact & Register & Login
    Shelters --> Pets
    Login --> Account
    Register --> Account
    Account --> Add & Edit & Handle & Fav & Prof & Withdraw & Out
    Pet --> Req & Fav & Edit
    Login -->|"admin"| Dash
    Dash --> Appr & APets & AReq & AShel & ASpec & AUsers & AMsg
```
