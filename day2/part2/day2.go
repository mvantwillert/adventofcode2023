package main

import (
	"bufio"
	"fmt"
	"os"
	"strconv"
	"strings"
)

func getGameBagContainment(session string) map[string]int {
	bag := make(map[string]int)

	colors := strings.Split(session, ",")

	for _, color := range colors {
		colorArray := strings.Split(strings.Trim(color, " "), " ")

		amount, err := strconv.Atoi(colorArray[0])
		color := colorArray[1]

		bag[color] += amount
		if err != nil {
			panic(err)
		}
	}

	return bag
}

func getLowestContainment(currentBag map[string]int, lowest map[string]int) map[string]int {
	for color, amount := range currentBag {
		current := lowest[color]
		if current == 0 {
			lowest[color] = amount
			continue
		}

		if amount > current {
			lowest[color] = amount
		}
	}
	return lowest
}

func main() {
	file, err := os.Open("./step2_data.txt")

	if err != nil {
		panic(err)
	}

	defer file.Close()

	var gameNumberSum int
	scanner := bufio.NewScanner(file)

	for scanner.Scan() {
		splittedString := strings.Split(scanner.Text(), ":")
		gamesWithColor := strings.Split(splittedString[1], ";")

		if err != nil {
			panic(err)
		}

		lowestContainment := map[string]int{}
		for _, game := range gamesWithColor {
			sessions := strings.Split(game, ":")

			for _, session := range sessions {
				bagContainment := getGameBagContainment(session)
				lowestContainment = getLowestContainment(bagContainment, lowestContainment)
			}
		}

		summedPower := 1
		for _, power := range lowestContainment {
			summedPower = summedPower * power
		}

		gameNumberSum += summedPower
	}

	response := fmt.Sprintf("The correct number is: %d", gameNumberSum)
	fmt.Print(response)
}
