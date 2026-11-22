# ERD – petadopt database

```mermaid
erDiagram
    USER ||--o{ PET : "lists"
    USER ||--o{ ADOPTIONREQUEST : "sends"
    PET ||--o{ ADOPTIONREQUEST : "receives"
    SPECIES ||--o{ PET : "classifies"
    SHELTER |o--o{ PET : "cares for (optional)"
    SHELTER |o--o{ USER : "employs (staff)"
    USER ||--o| ADOPTERPROFILE : "has"
    USER ||--o{ FAVOURITE : "saves"
    PET ||--o{ FAVOURITE : "is saved in"

    USER {
        int id PK
        varchar name
        varchar email UK
        varchar password "bcrypt hash"
        enum role "member / admin"
        int shelterid FK "nullable - NULL = private rehoming"
        varchar city "used when there is no shelter" "nullable - staff only"
        date datejoined
    }
    PET {
        int id PK
        varchar name
        int age
        enum gender
        text description
        varchar image "file name in images/pets"
        varchar imagecredit "photographer + licence"
        varchar imagesource "link to original photo"
        varchar breed
        enum size "Small / Medium / Large"
        enum energy "Low / Medium / High"
        tinyint vaccinated
        tinyint neutered
        varchar healthnotes
        enum approval "Pending / Approved / Rejected"
        varchar reviewnote "reason when rejected"
        tinyint goodwithkids
        tinyint goodwithdogs
        tinyint goodwithcats
        tinyint apartmentok
        enum status "Available / Reserved / Adopted"
        date dateadded
        int speciesid FK
        int shelterid FK
        int userid FK "nullable"
    }
    SPECIES {
        int id PK
        varchar name UK
    }
    SHELTER {
        int id PK
        varchar name
        varchar city
        varchar email
        varchar phone
        tinyint verified "checked by admin"
    }
    ADOPTIONREQUEST {
        int id PK
        text message
        date requestdate
        enum status "New / Interviewing / Approved / Rejected"
        date interviewdate "day the interview stage was reached (NULL = none)"
        date decisiondate "day approved / rejected (NULL = none)"
        int petid FK
        int userid FK
    }
    ADOPTERPROFILE {
        int userid PK,FK
        enum hometype
        tinyint householdagrees
        enum experience
        varchar otherpets
        enum budget
        datetime updated
    }
    FAVOURITE {
        int userid PK,FK
        int petid PK,FK
        datetime datesaved
    }
    LOGINATTEMPT {
        int id PK
        varchar email
        varchar ip
        datetime attempted "failed log in; 5 in 10 minutes = locked for a while"
    }
    MESSAGE {
        int id PK
        varchar name
        varchar email
        varchar subject
        text body
        datetime datesent
    }
```

Foreign key rules: deleting a species or shelter that still has pets is blocked (RESTRICT);
deleting a user keeps their pets (userid SET NULL) but removes their requests (CASCADE);
deleting a pet removes its requests (CASCADE).
`favourite` links user and pet many-to-many; its primary key is (userid, petid) so a pet can only be saved once
per member, and deleting either the user or the pet removes the favourite (CASCADE). `message` is standalone (contact form log).
`adopterprofile` is one-to-one with `user`: its primary key is also the foreign key `userid`, so each member can
have at most one profile (deleted with the user, CASCADE).
A listing is only public when `pet.approval = 'Approved'`. New listings and every edit by a member start as `Pending`; an admin approves or rejects them (a reason is saved in `reviewnote`).
`user.shelterid` links a member to the shelter they work for (one shelter has many staff, ON DELETE SET NULL). An admin sets it in
`admin/users.php`; nobody can set it for themselves, which is the verification step. There is no extra role, so role and shelter cannot disagree.
`pet.shelterid` is nullable: a pet without a shelter is a **private rehoming** and stores its own `city`.

Who may publish: admin = approved at once; staff of a *verified* shelter = approved at once for their own shelter's pets;
everyone else (members, staff of an unverified shelter) = `Pending` until an admin approves.

An adoption request moves New -> Interviewing -> Approved (it can be rejected at any point and a decision can be undone). `interviewdate` and
`decisiondate` are saved by `moveRequest()`, so the progress bar shows only what really happened (for example "Interview - skipped" when a request
was approved without an interview). A pet is `Reserved` only while one of its requests is `Approved`.
`adoptionrequest` has a UNIQUE key on (petid, userid): one request per person per pet, enforced by the database itself.
`loginattempt` is standalone: failed log ins (e-mail + IP + time) used to slow down password guessing.
