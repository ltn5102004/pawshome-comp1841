-- Source:
--   - MySQL (no date) CREATE TABLE Statement, MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/create-table.html
--   - MySQL (no date) FOREIGN KEY Constraints (ON DELETE RESTRICT / CASCADE / SET NULL), MySQL 8.0 Reference Manual. https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html
--   - Database design of the lab Jokes system (joke, author, category, joke_category) - COMP1841 lab materials (Prichard, 2026) - controller / template structure of the Jokes CRUD system
--   - Many-to-many join table (favourite) adapted from Butler, T. and Yank, K. (2017) PHP & MySQL: Novice to Ninja. 6th edn. SitePoint (joke_category table)
--   - Sample passwords are hashed with PHP password_hash (PHP Group, no date)
-- ============================================================
-- PawsHome - Pet Adoption Board
-- COMP1841 Web Programming 1 coursework
-- Import this file in phpMyAdmin (Import tab) or run it in the SQL tab.
-- ============================================================

CREATE DATABASE IF NOT EXISTS petadopt CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE petadopt;

-- Drop child tables first because of the foreign keys
DROP TABLE IF EXISTS loginattempt;
DROP TABLE IF EXISTS adopterprofile;
DROP TABLE IF EXISTS favourite;
DROP TABLE IF EXISTS adoptionrequest;
DROP TABLE IF EXISTS pet;
DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS species;
DROP TABLE IF EXISTS user;      -- user points to shelter, so it is dropped first
DROP TABLE IF EXISTS shelter;

-- ------------------------------------------------------------
-- user: people who can log in (members and admins)
-- ------------------------------------------------------------
CREATE TABLE user (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('member', 'admin') NOT NULL DEFAULT 'member',
    shelterid INT NULL,              -- the shelter this person works for (staff), NULL for other users; foreign key added below
    datejoined DATE NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (email)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- shelter: where the pet is currently looked after
-- ------------------------------------------------------------
CREATE TABLE shelter (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    verified TINYINT(1) NOT NULL DEFAULT 0,   -- 1 = checked by the PawsHome admin team
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- One shelter has many staff, each staff member works for one shelter (one-to-many).
-- The foreign key is added here because shelter did not exist yet when user was created.
ALTER TABLE user
    ADD CONSTRAINT fk_user_shelter FOREIGN KEY (shelterid) REFERENCES shelter (id) ON DELETE SET NULL;

-- ------------------------------------------------------------
-- species: dog, cat, rabbit ...
-- ------------------------------------------------------------
CREATE TABLE species (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (name)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- pet: the main entry of the system (was "joke" in the lab system)
-- ------------------------------------------------------------
CREATE TABLE pet (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NULL,
    imagecredit VARCHAR(255) NULL,   -- photographer + licence of sample photos (shown under the photo)
    imagesource VARCHAR(500) NULL,   -- link to the original photo page
    breed VARCHAR(80) NULL,
    size ENUM('Small', 'Medium', 'Large') NOT NULL DEFAULT 'Medium',
    energy ENUM('Low', 'Medium', 'High') NOT NULL DEFAULT 'Medium',
    vaccinated TINYINT(1) NOT NULL DEFAULT 0,       -- 1 = yes, 0 = no
    neutered TINYINT(1) NOT NULL DEFAULT 0,
    healthnotes VARCHAR(500) NULL,                  -- known health conditions
    goodwithkids TINYINT(1) NOT NULL DEFAULT 0,
    goodwithdogs TINYINT(1) NOT NULL DEFAULT 0,
    goodwithcats TINYINT(1) NOT NULL DEFAULT 0,
    apartmentok TINYINT(1) NOT NULL DEFAULT 0,      -- suitable for a flat
    status ENUM('Available', 'Reserved', 'Adopted') NOT NULL DEFAULT 'Available',
    -- A new listing is only shown on the public site after it has been reviewed
    approval ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    reviewnote VARCHAR(255) NULL,    -- reason shown to the owner when a listing is rejected
    dateadded DATE NOT NULL,
    speciesid INT NOT NULL,
    shelterid INT NULL,              -- NULL = private rehoming (a member listing a pet that is not with a shelter)
    city VARCHAR(100) NULL,          -- where a private rehoming pet is (shelter pets use the shelter's city)
    userid INT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (speciesid) REFERENCES species (id) ON DELETE RESTRICT,
    FOREIGN KEY (shelterid) REFERENCES shelter (id) ON DELETE RESTRICT,
    FOREIGN KEY (userid) REFERENCES user (id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- adoptionrequest: a member asks to adopt a pet
-- ------------------------------------------------------------
CREATE TABLE adoptionrequest (
    id INT NOT NULL AUTO_INCREMENT,
    message TEXT NOT NULL,
    requestdate DATE NOT NULL,
    status ENUM('New', 'Interviewing', 'Approved', 'Rejected') NOT NULL DEFAULT 'New',
    interviewdate DATE NULL,         -- the day the request first reached "Interviewing" (NULL = no interview happened)
    decisiondate DATE NULL,          -- the day it was approved or rejected (NULL = no decision yet)
    petid INT NOT NULL,
    userid INT NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY onerequestperpet (petid, userid),   -- one request per person per pet (also protects against double clicks)
    FOREIGN KEY (petid) REFERENCES pet (id) ON DELETE CASCADE,
    FOREIGN KEY (userid) REFERENCES user (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- favourite: pets a member has saved (many-to-many between user and pet)
-- The two foreign keys together are the primary key, so a member
-- can only save the same pet once.
-- ------------------------------------------------------------
CREATE TABLE favourite (
    userid INT NOT NULL,
    petid INT NOT NULL,
    datesaved DATETIME NOT NULL,
    PRIMARY KEY (userid, petid),
    FOREIGN KEY (userid) REFERENCES user (id) ON DELETE CASCADE,
    FOREIGN KEY (petid) REFERENCES pet (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- adopterprofile: the adopter's living situation, saved once and
-- reused with every adoption request (one-to-one with user)
-- ------------------------------------------------------------
CREATE TABLE adopterprofile (
    userid INT NOT NULL,
    hometype ENUM('House with garden', 'House without garden', 'Flat / apartment') NOT NULL,
    householdagrees TINYINT(1) NOT NULL DEFAULT 0,  -- everyone at home agrees to the adoption
    experience ENUM('First-time owner', 'Some experience', 'Experienced owner') NOT NULL,
    otherpets VARCHAR(255) NULL,
    budget ENUM('Under £50', '£50-£100', 'Over £100') NOT NULL,  -- monthly budget for the pet
    updated DATETIME NOT NULL,
    PRIMARY KEY (userid),
    FOREIGN KEY (userid) REFERENCES user (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- loginattempt: failed log in attempts (e-mail + IP + time). After 5 failures in 10 minutes the
-- log in form refuses that e-mail from that IP for a while, so passwords cannot be guessed quickly.
-- ------------------------------------------------------------
CREATE TABLE loginattempt (
    id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    attempted DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY lookup (email, ip, attempted)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- message: copy of every contact form message (so the admin can
-- still read them when mail() does not work on localhost)
-- ------------------------------------------------------------
CREATE TABLE message (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    datesent DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ============================================================
-- Sample data
-- Admin login:  admin@pawshome.test  /  Admin123!
-- Member login: anna@pawshome.test   /  Member123!
-- ============================================================

INSERT INTO user (id, name, email, password, role, datejoined) VALUES
(1, 'Site Admin', 'admin@pawshome.test', '$2y$12$02XBV6gb5vx3wf7.sn/OUO25R26NlgG.BwzDJTytA2upKhw1nZJC.', 'admin', '2026-09-20'),
(2, 'Anna Nguyen', 'anna@pawshome.test', '$2y$12$QKFK7jGqUhQuH299QmHaJuh6aUZ0B1.9m8hzjExuqSkxaycbpuszK', 'member', '2026-10-01'),
(3, 'Tom Baker', 'tom@pawshome.test', '$2y$12$QKFK7jGqUhQuH299QmHaJuh6aUZ0B1.9m8hzjExuqSkxaycbpuszK', 'member', '2026-10-05');

INSERT INTO shelter (id, name, city, email, phone) VALUES
(1, 'Greenwich Animal Rescue', 'London', 'hello@greenwichrescue.test', '020 7946 0101'),
(2, 'Kent Paws Sanctuary', 'Maidstone', 'info@kentpaws.test', '01622 960 202'),
(3, 'Riverside Cat & Dog Home', 'Oxford', 'contact@riverside.test', '01865 960 303'),
(4, 'Manchester Dogs & Cats Haven', 'Manchester', 'hello@mcrhaven.test', '0161 960 0404'),
(5, 'Bristol Harbourside Rescue', 'Bristol', 'info@bristolrescue.test', '0117 960 0505'),
(6, 'Brighton Seaside Animal Home', 'Brighton', 'adopt@brightonseaside.test', '01273 960 606'),
(7, 'Edinburgh Castle Pet Sanctuary', 'Edinburgh', 'care@edinsanctuary.test', '0131 960 0707'),
(8, 'Cardiff Bay Animal Trust', 'Cardiff', 'team@cardiffbaytrust.test', '029 2096 0808'),
(9, 'Leeds Second Chance Pets', 'Leeds', 'secondchance@leedspets.test', '0113 960 0909'),
(10, 'Birmingham Paws & Claws', 'Birmingham', 'hello@pawsclaws.test', '0121 960 1010'),
(11, 'Cambridge Small Pets Rescue', 'Cambridge', 'smallpets@cambridgerescue.test', '01223 960 111'),
(12, 'Liverpool Waterfront Shelter', 'Liverpool', 'info@waterfrontshelter.test', '0151 960 1212'),
(13, 'Norwich Exotic & Reptile Rescue', 'Norwich', 'exotics@norwichrescue.test', '01603 960 131'),
(14, 'York Minster Animal Welfare', 'York', 'welfare@yorkanimals.test', '01904 960 141'),
(15, 'Newcastle Tyneside Rescue Centre', 'Newcastle upon Tyne', 'adopt@tynesiderescue.test', '0191 960 1515');

INSERT INTO species (id, name) VALUES
(1, 'Dog'),
(2, 'Cat'),
(3, 'Rabbit'),
(4, 'Bird'),
(5, 'Hamster'),
(6, 'Guinea pig'),
(7, 'Tortoise'),
(8, 'Ferret');

INSERT INTO pet (id, name, age, gender, description, image, status, dateadded, speciesid, shelterid, userid) VALUES
(1, 'Biscuit', 3, 'Male', 'A friendly Golden Retriever who loves long walks and belly rubs. Good with children and other dogs.', 'biscuit.jpg', 'Available', '2026-10-02', 1, 1, 1),
(2, 'Luna', 2, 'Female', 'Calm and curious tabby cat. Luna enjoys sunny windows and would suit a quiet home.', 'luna.jpg', 'Available', '2026-10-04', 2, 1, 2),
(3, 'Thumper', 1, 'Male', 'Energetic lop-eared rabbit. Already litter trained and loves fresh greens.', 'thumper.jpg', 'Reserved', '2026-10-08', 3, 2, 2),
(4, 'Kiwi', 4, 'Female', 'A chatty budgie who can whistle a few tunes. Comes with her own cage.', 'kiwi.jpg', 'Available', '2026-10-10', 4, 3, 3),
(5, 'Max', 6, 'Male', 'Older Labrador looking for a relaxed retirement home. Fully house trained.', 'max.jpg', 'Adopted', '2026-09-28', 1, 2, 1),
(6, 'Peanut', 1, 'Female', 'Tiny Syrian hamster, very gentle and easy to handle. Perfect first pet.', 'peanut.jpg', 'Available', '2026-10-15', 5, 3, 3),
(7, 'Shadow', 5, 'Male', 'Independent black cat who warms up quickly once he trusts you.', 'shadow.jpg', 'Available', '2026-10-18', 2, 2, 1),
(8, 'Rocky', 4, 'Male', 'Handsome chocolate Labrador who adores fetch and swimming. Knows sit, stay and paw, and is gentle with older children.', 'rocky.jpg', 'Available', '2026-10-19', 1, 4, NULL),
(9, 'Daisy', 9, 'Female', 'Senior Beagle with a silver muzzle and a big heart. Enjoys short sniffy walks and long naps, ideal for a calm household.', 'daisy.jpg', 'Available', '2026-10-20', 1, 5, 2),
(10, 'Bella', 3, 'Female', 'Clever tri-colour Border Collie. Needs an active home with lots of exercise and brain games; already learning agility.', 'bella.jpg', 'Reserved', '2026-10-20', 1, 7, NULL),
(11, 'Oscar', 1, 'Male', 'Playful miniature Dachshund puppy. Loves digging in the sand and snuggling under blankets. Still working on house training.', 'oscar.jpg', 'Available', '2026-10-21', 1, 6, 3),
(12, 'Nova', 1, 'Female', 'Young Siberian Husky with striking eyes. Very friendly and vocal; needs a secure garden and an owner who loves running.', 'nova.jpg', 'Available', '2026-10-21', 1, 15, NULL),
(13, 'Rosie', 5, 'Female', 'Sweet Cavalier King Charles Spaniel who loves lying in the grass. Fine with cats and very affectionate with everyone.', 'rosie.jpg', 'Available', '2026-10-22', 1, 1, 1),
(14, 'Alfie', 6, 'Male', 'Cheerful terrier cross with a cheeky personality. House trained, travels well in the car and enjoys a good belly rub.', 'alfie.jpg', 'Adopted', '2026-10-01', 1, 9, NULL),
(15, 'Bruno', 5, 'Male', 'Loyal German Shepherd who has completed basic obedience. Best as the only pet with an experienced owner.', 'bruno.jpg', 'Available', '2026-10-23', 1, 10, NULL),
(16, 'Coco', 3, 'Female', 'French Bulldog who surprisingly loves the water (in her life jacket!). Snores a little and loves people.', 'coco.jpg', 'Available', '2026-10-23', 1, 6, 2),
(17, 'Winston', 2, 'Male', 'Happy Pug who is always ready for a cuddle. Does not need long walks and would suit flat living.', 'winston.jpg', 'Reserved', '2026-10-24', 1, 12, NULL),
(18, 'Milo', 4, 'Male', 'Calm white British Shorthair with blue eyes. Prefers a quiet home and a sunny windowsill.', 'milo.jpg', 'Available', '2026-10-24', 2, 11, NULL),
(19, 'Tiger', 6, 'Male', 'Gentle giant Maine Coon with a fluffy mane. Needs regular brushing and gets on well with respectful dogs.', 'tiger.jpg', 'Available', '2026-10-25', 2, 14, 3),
(20, 'Misty', 3, 'Female', 'Elegant oriental-type Siamese who will talk to you all day. Loves climbing and interactive toys.', 'misty.jpg', 'Available', '2026-10-25', 2, 8, NULL),
(21, 'Willow', 3, 'Female', 'Fluffy Ragdoll who goes floppy when picked up. Very relaxed and happy to be an indoor cat.', 'willow.jpg', 'Adopted', '2026-10-05', 2, 2, NULL),
(22, 'Pepper', 2, 'Female', 'Curious tabby who loves exploring outside, even in the snow. Microchipped and fully vaccinated.', 'pepper.jpg', 'Available', '2026-10-26', 2, 7, 1),
(23, 'Ginger', 2, 'Male', 'Friendly ginger tabby who was found with his tabby brother. They would love to be adopted together.', 'ginger.jpg', 'Available', '2026-10-26', 2, 3, NULL),
(24, 'Clover', 2, 'Female', 'Pretty spotted rabbit who enjoys running around the garden and eating dandelions. Litter trained.', 'clover.jpg', 'Available', '2026-10-27', 3, 11, NULL),
(25, 'Hazel', 1, 'Female', 'Young black and white rabbit. A little shy at first but loves fresh hay and gentle strokes.', 'hazel.jpg', 'Available', '2026-10-27', 3, 9, 2),
(26, 'Sunny', 3, 'Male', 'Cheerful cockatiel who whistles along to the radio. Hand tame and happy to sit on your shoulder.', 'sunny.jpg', 'Available', '2026-10-28', 4, 5, NULL),
(27, 'Mango', 2, 'Female', 'Colourful lovebird looking for a home with plenty of space to fly and a lot of attention.', 'mango.jpg', 'Reserved', '2026-10-28', 4, 13, NULL),
(28, 'Nibbles', 1, 'Male', 'Tiny, inquisitive hamster who is easy to handle. Comes with his cage, wheel and a bag of food.', 'nibbles.jpg', 'Available', '2026-10-29', 5, 10, 3),
(29, 'Pip', 2, 'Male', 'Ginger guinea pig who squeaks loudly at feeding time. Guinea pigs are social, so Pip would like a friend.', 'pip.jpg', 'Available', '2026-10-29', 6, 11, NULL),
(30, 'Pumpkin', 1, 'Female', 'Tri-colour guinea pig who loves fresh vegetables and grazing on the lawn in summer.', 'pumpkin.jpg', 'Available', '2026-10-30', 6, 4, 1),
(31, 'Sheldon', 25, 'Male', 'Spur-thighed tortoise looking for a garden with a secure enclosure. Tortoises can live for decades, so this is a long-term commitment.', 'sheldon.jpg', 'Available', '2026-10-30', 7, 13, NULL),
(32, 'Bandit', 2, 'Male', 'Playful ferret who loves tunnels and hammocks. Already used to a harness and lead.', 'bandit.jpg', 'Available', '2026-10-31', 8, 12, NULL);

-- The sample pets have already been checked
UPDATE pet SET approval = 'Approved';

-- Photo credits for the sample photos (Wikimedia Commons, CC licences require attribution)
UPDATE pet SET imagecredit = 'Dktue, CC0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Portrait_of_a_labrador_retriever.jpg' WHERE id = 8;
UPDATE pet SET imagecredit = 'Benoit Brummer (Trougnouf), CC BY 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Beagle_in_Viroinval_(DSC04556).jpg' WHERE id = 9;
UPDATE pet SET imagecredit = 'Kreuzschnabel, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Border-Collie-tri-colour-face-1.jpg' WHERE id = 10;
UPDATE pet SET imagecredit = 'William Crochot, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Teckel_-_39.jpg' WHERE id = 11;
UPDATE pet SET imagecredit = 'MrPanyGoff, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Juvenile_Siberian_Husky.jpg' WHERE id = 12;
UPDATE pet SET imagecredit = 'Filip B, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Dog_In_The_Grass.jpg' WHERE id = 13;
UPDATE pet SET imagecredit = 'Marius Vassnes, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Jack_Russel_side_profile.jpg' WHERE id = 14;
UPDATE pet SET imagecredit = 'Jakub Hałun, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:20110425_German_Shepherd_Dog_8505.jpg' WHERE id = 15;
UPDATE pet SET imagecredit = 'W.carter, CC0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:French_bulldog_in_life_jacket.jpg' WHERE id = 16;
UPDATE pet SET imagecredit = 'Augustus Binu, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Pug_-_1_year_Old.jpg' WHERE id = 17;
UPDATE pet SET imagecredit = 'Jakub Hałun, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:20170604_kot_na_wystawie_w_Krakowie_7971.jpg' WHERE id = 18;
UPDATE pet SET imagecredit = 'ales brad, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Maine_Coon_Cat_List_Characteristics.jpg' WHERE id = 19;
UPDATE pet SET imagecredit = 'Felinlove, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Siamese_cat,_female.jpg' WHERE id = 20;
UPDATE pet SET imagecredit = 'BlackIceNRW, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Ragdoll_Kater,_drei_Jahre_alt,_RAG_n_21_seal-tabby-colourpoint,_Januar_2015.JPG' WHERE id = 21;
UPDATE pet SET imagecredit = 'Von.grzanka, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Felis_catus-cat_on_snow.jpg' WHERE id = 22;
UPDATE pet SET imagecredit = 'Laitche, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Tabby_cats_of_%C5%8Cizumi_Ryokuchi_Park,_January_2019_-_8816.jpg' WHERE id = 23;
UPDATE pet SET imagecredit = 'Friedrich Haag, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Heimtier_004_2023_08_26.jpg' WHERE id = 24;
UPDATE pet SET imagecredit = 'Diego Delso, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Conejo_com%C3%BAn_(Oryctolagus_cuniculus),_Tierpark_Hellabrunn,_M%C3%BAnich,_Alemania,_2012-06-17,_DD_02.JPG' WHERE id = 25;
UPDATE pet SET imagecredit = 'Heba Aisha, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:My_Pet_Cockatiel.jpg' WHERE id = 26;
UPDATE pet SET imagecredit = 'Basotxerri, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Osnabr%C3%BCck_-_Zoo_-_Russk%C3%B6pfchen_01.jpg' WHERE id = 27;
UPDATE pet SET imagecredit = 'Augustus Binu, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Baby_Hamster_-_2_Weeks_Old.jpg' WHERE id = 28;
UPDATE pet SET imagecredit = 'Gzen92, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Cochon_d%27Inde_(Cavia_porcellus)_(2).jpg' WHERE id = 29;
UPDATE pet SET imagecredit = 'Diego Delso, CC BY-SA 3.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Cobaya_(Cavia_porcellus),_Tierpark_Hellabrunn,_M%C3%BAnich,_Alemania,_2012-06-17,_DD_01.JPG' WHERE id = 30;
UPDATE pet SET imagecredit = 'Charles J. Sharp, CC BY-SA 4.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Spur-thighed_tortoise_(Testudo_graeca_ibera)_Babadag.jpg' WHERE id = 31;
UPDATE pet SET imagecredit = 'Anthony O''Neil, CC BY-SA 2.0, via Wikimedia Commons', imagesource = 'https://commons.wikimedia.org/wiki/File:Pet_ferret_-_geograph.org.uk_-_8007550.jpg' WHERE id = 32;


-- Health, personality and compatibility details for each pet
UPDATE pet SET breed = 'Golden Retriever', size = 'Large', energy = 'High', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 1, goodwithcats = 0, apartmentok = 0 WHERE id = 1;
UPDATE pet SET breed = 'Tabby', size = 'Small', energy = 'Low', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 1, apartmentok = 1 WHERE id = 2;
UPDATE pet SET breed = 'Lop rabbit', size = 'Small', energy = 'Medium', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 3;
UPDATE pet SET breed = 'Budgerigar', size = 'Small', energy = 'Medium', vaccinated = 0, neutered = 0, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 4;
UPDATE pet SET breed = 'Labrador Retriever', size = 'Large', energy = 'Low', vaccinated = 1, neutered = 1, healthnotes = 'Mild arthritis in his back legs; takes a daily joint supplement.', goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 0 WHERE id = 5;
UPDATE pet SET breed = 'Syrian hamster', size = 'Small', energy = 'Medium', vaccinated = 0, neutered = 0, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 6;
UPDATE pet SET breed = 'Domestic shorthair', size = 'Medium', energy = 'Medium', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 0, goodwithdogs = 0, goodwithcats = 1, apartmentok = 1 WHERE id = 7;
UPDATE pet SET breed = 'Labrador Retriever', size = 'Large', energy = 'High', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 1, goodwithcats = 0, apartmentok = 0 WHERE id = 8;
UPDATE pet SET breed = 'Beagle', size = 'Medium', energy = 'Low', vaccinated = 1, neutered = 1, healthnotes = 'Senior: needs a check-up every six months and a light diet.', goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 1 WHERE id = 9;
UPDATE pet SET breed = 'Border Collie', size = 'Medium', energy = 'High', vaccinated = 1, neutered = 0, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 1, goodwithcats = 0, apartmentok = 0 WHERE id = 10;
UPDATE pet SET breed = 'Miniature Dachshund', size = 'Small', energy = 'Medium', vaccinated = 1, neutered = 0, healthnotes = 'Second vaccination due in two weeks.', goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 1 WHERE id = 11;
UPDATE pet SET breed = 'Siberian Husky', size = 'Large', energy = 'High', vaccinated = 1, neutered = 0, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 1, goodwithcats = 0, apartmentok = 0 WHERE id = 12;
UPDATE pet SET breed = 'Cavalier King Charles Spaniel', size = 'Small', energy = 'Low', vaccinated = 1, neutered = 1, healthnotes = 'Heart murmur (grade 2) - needs a yearly heart check.', goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 1 WHERE id = 13;
UPDATE pet SET breed = 'Jack Russell Terrier cross', size = 'Small', energy = 'High', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 1, goodwithcats = 0, apartmentok = 1 WHERE id = 14;
UPDATE pet SET breed = 'German Shepherd', size = 'Large', energy = 'High', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 0, goodwithdogs = 0, goodwithcats = 0, apartmentok = 0 WHERE id = 15;
UPDATE pet SET breed = 'French Bulldog', size = 'Small', energy = 'Medium', vaccinated = 1, neutered = 1, healthnotes = 'Short nose: must not be walked in hot weather.', goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 1 WHERE id = 16;
UPDATE pet SET breed = 'Pug', size = 'Small', energy = 'Low', vaccinated = 1, neutered = 1, healthnotes = 'Short nose: can snore and overheat easily.', goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 1 WHERE id = 17;
UPDATE pet SET breed = 'British Shorthair', size = 'Medium', energy = 'Low', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 1, apartmentok = 1 WHERE id = 18;
UPDATE pet SET breed = 'Maine Coon', size = 'Large', energy = 'Medium', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 0 WHERE id = 19;
UPDATE pet SET breed = 'Siamese', size = 'Medium', energy = 'High', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 1, apartmentok = 1 WHERE id = 20;
UPDATE pet SET breed = 'Ragdoll', size = 'Medium', energy = 'Low', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 1, goodwithcats = 1, apartmentok = 1 WHERE id = 21;
UPDATE pet SET breed = 'Tabby', size = 'Medium', energy = 'High', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 1, apartmentok = 0 WHERE id = 22;
UPDATE pet SET breed = 'Ginger tabby', size = 'Medium', energy = 'Medium', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 1, apartmentok = 1 WHERE id = 23;
UPDATE pet SET breed = 'Domestic rabbit', size = 'Small', energy = 'Medium', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 24;
UPDATE pet SET breed = 'Dutch rabbit', size = 'Small', energy = 'Low', vaccinated = 1, neutered = 0, healthnotes = NULL, goodwithkids = 0, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 25;
UPDATE pet SET breed = 'Cockatiel', size = 'Small', energy = 'Medium', vaccinated = 0, neutered = 0, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 26;
UPDATE pet SET breed = 'Rosy-faced lovebird', size = 'Small', energy = 'High', vaccinated = 0, neutered = 0, healthnotes = NULL, goodwithkids = 0, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 27;
UPDATE pet SET breed = 'Syrian hamster', size = 'Small', energy = 'Medium', vaccinated = 0, neutered = 0, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 28;
UPDATE pet SET breed = 'Abyssinian guinea pig', size = 'Small', energy = 'Medium', vaccinated = 0, neutered = 1, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 29;
UPDATE pet SET breed = 'Tri-colour guinea pig', size = 'Small', energy = 'Medium', vaccinated = 0, neutered = 0, healthnotes = NULL, goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 30;
UPDATE pet SET breed = 'Spur-thighed tortoise', size = 'Small', energy = 'Low', vaccinated = 0, neutered = 0, healthnotes = 'Hibernates in winter; needs a vet check before hibernation.', goodwithkids = 1, goodwithdogs = 0, goodwithcats = 0, apartmentok = 0 WHERE id = 31;
UPDATE pet SET breed = 'Ferret', size = 'Small', energy = 'High', vaccinated = 1, neutered = 1, healthnotes = NULL, goodwithkids = 0, goodwithdogs = 0, goodwithcats = 0, apartmentok = 1 WHERE id = 32;

-- Shelters checked by the admin team (two are still waiting to be verified)
UPDATE shelter SET verified = 1 WHERE id NOT IN (13, 14);

INSERT INTO adopterprofile (userid, hometype, householdagrees, experience, otherpets, budget, updated) VALUES
(2, 'Flat / apartment', 1, 'Some experience', 'One elderly cat', '£50-£100', '2026-10-20 09:00:00'),
(3, 'House with garden', 1, 'Experienced owner', 'None at the moment', 'Over £100', '2026-10-12 08:30:00');

-- ------------------------------------------------------------
-- Shelter staff, private rehoming and the review queue (sample data)
-- Staff login: staff.greenwich@pawshome.test or staff.york@pawshome.test  /  Staff123!
-- ------------------------------------------------------------

-- A listing posted by an ordinary member is a PRIVATE REHOMING: it is not attached to a shelter.
-- The city of the shelter is kept so the pet can still be found by area.
UPDATE pet
    JOIN shelter ON shelter.id = pet.shelterid
    JOIN user ON user.id = pet.userid AND user.role = 'member' AND user.shelterid IS NULL
SET pet.city = shelter.city, pet.shelterid = NULL;

-- Staff accounts. The admin links a person to a shelter in admin/users.php (that step is the verification).
INSERT INTO user (id, name, email, password, role, shelterid, datejoined) VALUES
(4, 'Greenwich Rescue Staff', 'staff.greenwich@pawshome.test', '$2y$10$.542QxMLHKThX3SJPkGCMuDFzm2bkLV1oqTqkchEvZ2AjbAUdCWym', 'member', 1, '2026-10-02'),
(5, 'York Welfare Staff', 'staff.york@pawshome.test', '$2y$10$.542QxMLHKThX3SJPkGCMuDFzm2bkLV1oqTqkchEvZ2AjbAUdCWym', 'member', 14, '2026-10-03');

-- Rosie is listed by Greenwich staff. Teddy comes from York, which is not verified yet, so he waits for review.
UPDATE pet SET userid = (SELECT id FROM user WHERE email = 'staff.greenwich@pawshome.test') WHERE id = 13;
INSERT INTO pet (id, name, age, gender, description, image, status, approval, dateadded, speciesid, shelterid, userid,
                 breed, size, energy, vaccinated, neutered, healthnotes, goodwithkids, goodwithdogs, goodwithcats, apartmentok) VALUES
(33, 'Teddy', 3, 'Male', 'Gentle Staffordshire Bull Terrier cross who loves people and tennis balls. Needs an owner who can give him a daily run.', NULL, 'Available', 'Pending', '2026-11-04', 1, 14,
 (SELECT id FROM user WHERE email = 'staff.york@pawshome.test'),
 'Staffordshire Bull Terrier cross', 'Medium', 'Medium', 1, 0, NULL, 1, 0, 0, 0);

INSERT INTO adoptionrequest (id, message, requestdate, status, petid, userid) VALUES
(1, 'We have a large garden and two children who would love Thumper.', '2026-10-12', 'Approved', 3, 3),
(2, 'I work from home and can give Biscuit plenty of walks.', '2026-10-20', 'Interviewing', 1, 2),
(3, 'Kiwi would be great company for my mum.', '2026-10-21', 'New', 4, 2),
(4, 'Our family has a big garden and we take long walks every day.', '2026-10-28', 'New', 8, 3),
(5, 'I am retired and at home all day, perfect for an older dog like Daisy.', '2026-10-29', 'Interviewing', 9, 3),
(6, 'We already have one guinea pig who needs a friend.', '2026-10-30', 'New', 29, 2);

INSERT INTO favourite (userid, petid, datesaved) VALUES
(2, 2, '2026-10-20 09:00:00'),
(2, 7, '2026-10-21 18:30:00'),
(3, 2, '2026-10-22 12:00:00'),
(2, 8, '2026-10-24 10:00:00'),
(2, 19, '2026-10-26 20:15:00'),
(3, 12, '2026-10-25 09:30:00'),
(3, 16, '2026-10-27 14:00:00'),
(1, 31, '2026-10-31 11:00:00');

-- Dates of the stages that were reached (the progress bar only shows what really happened)
UPDATE adoptionrequest SET interviewdate = '2026-10-14', decisiondate = '2026-10-16' WHERE id = 1;
UPDATE adoptionrequest SET interviewdate = '2026-10-22' WHERE id = 2;
UPDATE adoptionrequest SET interviewdate = '2026-10-31' WHERE id = 5;

-- Every pet that is Reserved or Adopted through the site has an approved request that explains it
-- (Alfie and Willow were adopted outside the site, so they have no request)
INSERT INTO adoptionrequest (id, message, requestdate, status, interviewdate, decisiondate, petid, userid) VALUES
(7, 'Our family has kept Border Collies before and we have a big garden.', '2026-10-23', 'Approved', '2026-10-25', '2026-10-27', 10, 3),
(8, 'I live in a flat and a calm pug like Winston would suit me well.', '2026-10-25', 'Approved', '2026-10-27', '2026-10-29', 17, 2),
(9, 'I have looked after lovebirds for years and have a large aviary.', '2026-10-27', 'Approved', '2026-10-29', '2026-10-31', 27, 3),
(10, 'Max would be a calm companion for my retired father.', '2026-10-01', 'Approved', '2026-10-03', '2026-10-05', 5, 2);

INSERT INTO message (id, name, email, subject, body, datesent) VALUES
(1, 'Jane Smith', 'jane@example.com', 'Volunteering', 'Hello, can I volunteer at one of the shelters at weekends?', '2026-10-22 10:15:00');

-- LIM-03 (22 Nov 2026): password reset links (see database/add_passwordreset.sql for notes)
CREATE TABLE IF NOT EXISTS `passwordreset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `tokenhash` char(64) NOT NULL,
  `expires` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tokenhash` (`tokenhash`),
  KEY `userid` (`userid`),
  CONSTRAINT `passwordreset_user` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
