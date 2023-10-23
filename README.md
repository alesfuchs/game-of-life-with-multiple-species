# Game of life with multiple species

## Game rules

Consider a representation of a "world" as an n by n matrix. Each element in the matrix may contain 1 organism. Each organism lives, dies and reproduces according to the following set of rules:

- If there are two or three organisms of the same type living in the elements surrounding an organism of the same, type then it may survive.
- If there are less than two organisms of one type surrounding one of the same type then it will die due to isolation.
- If there are four or more organisms of one type surrounding one of the same type then it will die due to overcrowding.
- If there are exactly three organisms of one type surrounding one element, they may give birth into that element. The new organism is the
same type as its parents. If this condition is true for more than one
species on the same element then species type for the new element is chosen randomly.
- If two organisms occupy one element, one of them must die (chosen randomly) (only to resolve initial conflicts).

The "world" and initial distribution of organisms within it is defined by data in a JSON format having the following structure:

```
{
    "dimension": 5,
    "speciesCount": 3,
    "iterationsCount": 10,
    "organisms":
    [
        {
            "x_pos": 1,
            "y_pos": 1,
            "species": "blue"
        },
        {
            "x_pos": 1,
            "y_pos": 1,
            "species": "red"
        },
        {
            "x_pos": 4,
            "y_pos": 1,
            "species": "blue"
        },
        {
            "x_pos": 3,
            "y_pos": 3,
            "species": "red"
        },
        {
            "x_pos": 3,
            "y_pos": 3,
            "species": "green"
        }
    ]
}
```

## Playing the game

1. Run the localhost environment as described below
2. Go to https://localhost
3. Provide JSON data with the initial matrix setup and the number of iterations after which you would like to pause the simulation and check the result.
4. Hit the Submit button and enjoy!

## Running the localhost environment

1. Clone the repository `git clone https://github.com/alesfuchs/game-of-life-with-multiple-species.git`
2. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose up --pull -d --wait`
3. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
4. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Technical notes

The git repository was forked from https://github.com/dunglas/symfony-docker for a quick start.

## License

Symfony Docker is available under the MIT License.
