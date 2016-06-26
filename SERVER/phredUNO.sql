/*

  The MIT License (MIT)

  Copyright (c) 2016+ Kevin Olinger <https://kevyn.lu>

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.

*/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sql1phredUNO`
--

-- --------------------------------------------------------

--
-- Table structure for table `uno_account`
--

CREATE TABLE IF NOT EXISTS `uno_account` (
`accountID` int(11) NOT NULL,
  `token` varchar(96) NOT NULL,
  `username` varchar(50) NOT NULL,
  `blowfish` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `regDate` date NOT NULL,
  `regIP` varchar(25) NOT NULL,
  `language` varchar(2) NOT NULL,
  `userGroup` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uno_game`
--

CREATE TABLE IF NOT EXISTS `uno_game` (
`gameID` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `numPlayers` int(2) NOT NULL,
  `numRounds` int(2) NOT NULL,
  `numCards` int(2) NOT NULL,
  `created` datetime NOT NULL,
  `started` datetime NOT NULL,
  `ended` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uno_group`
--

CREATE TABLE IF NOT EXISTS `uno_group` (
`groupID` int(11) NOT NULL,
  `description` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uno_language`
--

CREATE TABLE IF NOT EXISTS `uno_language` (
  `langCode` varchar(2) NOT NULL,
  `description` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uno_play`
--

CREATE TABLE IF NOT EXISTS `uno_play` (
  `gameID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uno_systemtoken`
--

CREATE TABLE IF NOT EXISTS `uno_systemtoken` (
`tokenID` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `purpose` varchar(255) NOT NULL DEFAULT 'Not defined',
  `permRegister` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uno_token_usage`
--

CREATE TABLE IF NOT EXISTS `uno_token_usage` (
`usageID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `IP` varchar(25) NOT NULL,
  `device` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `uno_account`
--
ALTER TABLE `uno_account`
 ADD PRIMARY KEY (`accountID`), ADD KEY `language` (`language`), ADD KEY `group` (`userGroup`);

--
-- Indexes for table `uno_game`
--
ALTER TABLE `uno_game`
 ADD PRIMARY KEY (`gameID`);

--
-- Indexes for table `uno_group`
--
ALTER TABLE `uno_group`
 ADD PRIMARY KEY (`groupID`), ADD KEY `groupID` (`groupID`);

--
-- Indexes for table `uno_language`
--
ALTER TABLE `uno_language`
 ADD PRIMARY KEY (`langCode`);

--
-- Indexes for table `uno_play`
--
ALTER TABLE `uno_play`
 ADD PRIMARY KEY (`gameID`,`accountID`), ADD KEY `accountID` (`accountID`);

--
-- Indexes for table `uno_systemtoken`
--
ALTER TABLE `uno_systemtoken`
 ADD PRIMARY KEY (`tokenID`);

--
-- Indexes for table `uno_token_usage`
--
ALTER TABLE `uno_token_usage`
 ADD PRIMARY KEY (`usageID`), ADD KEY `accountID` (`accountID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `uno_account`
--
ALTER TABLE `uno_account`
MODIFY `accountID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `uno_game`
--
ALTER TABLE `uno_game`
MODIFY `gameID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `uno_group`
--
ALTER TABLE `uno_group`
MODIFY `groupID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `uno_systemtoken`
--
ALTER TABLE `uno_systemtoken`
MODIFY `tokenID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `uno_token_usage`
--
ALTER TABLE `uno_token_usage`
MODIFY `usageID` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
