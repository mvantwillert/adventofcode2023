package main

import (
	"bufio"
	"fmt"
	"os"
	"regexp"
	"strconv"
	"strings"
)

func getGameBagContainment(session string) map[string]int {
	bag := make(map[string]int)

	colors := strings.Split(session, ",")

	for _, color := range colors {
		colorArray := strings.Split(strings.Trim(color, " "), " ")

		// amount := colorArray[0]
		amount, err := strconv.Atoi(colorArray[0])
		color := colorArray[1]

		// fmt.Println(bagContainment)
		bag[color] += amount
		if err != nil {
			panic(err)
		}
	}

	return bag
}

func checkGameCanBeDone(bag map[string]int) bool {
	maxBagContaintment := map[string]int{"red": 12, "blue": 14, "green": 13}
	for color, amount := range bag {
		if amount > maxBagContaintment[color] {
			return false
		}
	}
	fmt.Println(bag)
	return true
}

func main() {
	file, err := os.Open("./step1_data.txt")

	if err != nil {
		panic(err)
	}

	defer file.Close()

	var gameNumberSum int
	scanner := bufio.NewScanner(file)

	for scanner.Scan() {
		splittedString := strings.Split(scanner.Text(), ":")
		gamesWithColor := strings.Split(splittedString[1], ";")

		re := regexp.MustCompile("[0-9]+")
		gameNumber, err := strconv.Atoi(re.FindAllString(splittedString[0], -1)[0])

		if err != nil {
			panic(err)
		}

		count := true
		for _, game := range gamesWithColor {
			sessions := strings.Split(game, ":")

			for _, session := range sessions {
				bagContainment := getGameBagContainment(session)
				canBeDone := checkGameCanBeDone(bagContainment)
				if !canBeDone {
					count = false
					break
				}
			}
		}

		if count {
			gameNumberSum += gameNumber
		}
	}

	response := fmt.Sprintf("The correct number is: %d", gameNumberSum)
	fmt.Print(response)
}
